<?php

namespace App\Http\Controllers\Admin;

use App\Services\KasproService;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;
use Carbon\Carbon;

class OrderController extends Controller
{
    private function applyBranchFilter($query)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $user->branch_id) {
            $query->where('branch_id', $user->branch_id);
        } elseif ($user->isSuperAdmin()) {
            $pusat = Branch::where('code', 'PUSAT')->first();
            if ($pusat) {
                $query->where(function ($q) use ($pusat) {
                    $q->where('branch_id', '!=', $pusat->id)
                      ->orWhereNull('branch_id');
                });
            }
        }
        return $query;
    }

    public function index(Request $request)
    {
        $query = Order::with(['customer', 'items.product', 'branch', 'user', 'cashierShift'])
            ->orderBy('created_at', 'desc');
            
        $query = $this->applyBranchFilter($query);
        
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        
        $period = $request->get('period', 'all');
        if ($period === 'today') {
            $query->whereDate('date', \Carbon\Carbon::today());
        } elseif ($period === 'week') {
            $query->whereBetween('date', [\Carbon\Carbon::now()->startOfWeek(), \Carbon\Carbon::now()->endOfWeek()]);
        } elseif ($period === 'month') {
            $query->whereYear('date', \Carbon\Carbon::now()->year)
                  ->whereMonth('date', \Carbon\Carbon::now()->month);
        }
        
        $orders = $query->paginate(20)->withQueryString();

        $statsQuery = Order::query();
        $statsQuery = $this->applyBranchFilter($statsQuery);
        
        if ($request->filled('branch_id')) {
            $statsQuery->where('branch_id', $request->branch_id);
        }
        
        if ($period === 'today') {
            $statsQuery->whereDate('date', \Carbon\Carbon::today());
        } elseif ($period === 'week') {
            $statsQuery->whereBetween('date', [\Carbon\Carbon::now()->startOfWeek(), \Carbon\Carbon::now()->endOfWeek()]);
        } elseif ($period === 'month') {
            $statsQuery->whereYear('date', \Carbon\Carbon::now()->year)
                        ->whereMonth('date', \Carbon\Carbon::now()->month);
        }

        $stats = [
            'total'      => (clone $statsQuery)->count(),
            'paid'       => (clone $statsQuery)->where('payment_status', 'paid')->count(),
            'receivable' => (clone $statsQuery)->whereIn('payment_status', ['unpaid', 'partial'])->count(),
        ];

        $branches = [];
        if (Auth::user()->isSuperAdmin()) {
            $branches = Branch::where('code', '!=', 'PUSAT')->orderBy('name')->get();
        }

        return view('admin.orders.index', compact('orders', 'stats', 'branches', 'period'));
    }

    public function selectCustomer()
    {
        $customers = Customer::orderBy('name')->get();
        return view('admin.orders.select-customer', compact('customers'));
    }

    public function perCustomer(Request $request, Customer $customer)
    {
        $query = Order::where('customer_id', $customer->id)
            ->with(['items.product', 'branch', 'user'])
            ->orderBy('date', 'desc');

        $query = $this->applyBranchFilter($query);

        if ($request->filled('search')) {
            $query->whereHas(
                'items.product',
                fn($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
            );
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay(),
            ]);
        } elseif ($request->filter === 'today') {
            $query->whereDate('date', Carbon::today());
        } elseif ($request->filter === 'month') {
            $query->whereMonth('date', Carbon::now()->month)
                ->whereYear('date', Carbon::now()->year);
        }

        $orders = $query->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return view('admin.orders.partials.table', compact('orders'))->render();
        }

        $statsQuery = Order::where('customer_id', $customer->id);
        $statsQuery = $this->applyBranchFilter($statsQuery);
        $total = (clone $statsQuery)->count();

        return view('admin.orders.per-customer', compact('customer', 'orders', 'total'));
    }

    public function create(Customer $customer)
    {
        $products = Product::with('unit')->where('is_active', true)->orderBy('name')->get();
        return view('admin.orders.create', compact('customer', 'products'));
    }

    public function store(Request $request, Customer $customer, KasproService $kaspro)
    {
        $request->validate([
            'date'                   => 'required|date',
            'paid'                   => 'required|numeric|min:0',
            'payment_method'         => 'nullable|string',
            'notes'                  => 'nullable|string',
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|exists:products,id',
            'items.*.quantity'       => 'required|integer|min:1',
            'items.*.selling_price'  => 'required|numeric|min:0',
            'items.*.discount'       => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $customer, $kaspro) {
            $subtotal = 0;

            foreach ($request->items as $item) {
                $lineDiscount = $item['discount'] ?? 0;
                $subtotal += ($item['quantity'] * $item['selling_price']) - $lineDiscount;
            }

            $discount    = $request->discount ?? 0;
            $totalAmount = $subtotal - $discount;
            $totalPaid   = $request->paid;
            $remaining   = max(0, $totalAmount - $totalPaid);

            $order = Order::create([
                'customer_id'          => $customer->id,
                'user_id'              => Auth::id(),
                'date'                 => $request->date,
                'source'               => 'manual',
                'subtotal'             => $subtotal,
                'discount'             => $discount,
                'total_amount'         => $totalAmount,
                'total_paid'           => $totalPaid,
                'remaining_receivable' => $remaining,
                'payment_status'       => $remaining <= 0 ? 'paid' : ($totalPaid > 0 ? 'partial' : 'unpaid'),
                'payment_method'       => $request->payment_method ?? 'cash',
                'notes'                => $request->notes,
            ]);

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);



                $lineDiscount = $item['discount'] ?? 0;
                $lineSubtotal = ($item['quantity'] * $item['selling_price']) - $lineDiscount;

                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $item['product_id'],
                    'quantity'      => $item['quantity'],
                    'selling_price' => $item['selling_price'],
                    'cost_price'    => $product->purchase_price, // Ã¢Å“â€¦ TAMBAHAN PENTING
                    'discount'      => $lineDiscount,
                    'subtotal'      => $lineSubtotal,
                ]);

                  // Auto-kategorisasi (untuk pemasukan)
                $categorization = $kaspro->categorize($request->notes ?? 'Penjualan ke ' . $customer->name, 'INCOME');
                $order->kaspro_category = $categorization['category'];
                $order->kaspro_confidence = $categorization['confidence'];
                
                // Deteksi anomali
                $anomaly = $kaspro->detectAnomaly($order, 'INCOME');
                $order->kaspro_is_anomaly = $anomaly['is_anomaly'];
                $order->kaspro_risk_score = $anomaly['risk_score'];
                
                $order->save();
                
                // Cek alert
                $kaspro->checkAndCreateAlerts();
            }
        });

        return redirect()->route('admin.orders.per-customer', $customer)
            ->with('success', 'Order berhasil disimpan.');
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'user', 'items.product', 'payments']);
        return view('admin.orders.show', compact('order'));
    }

    public function downloadPdf(Request $request, Customer $customer)
    {
        $query = Order::where('customer_id', $customer->id)
            ->with('items.product')
            ->orderBy('date', 'desc');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay(),
            ]);
        }

        $orders = $query->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.orders.pdf', compact('customer', 'orders'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("Orders_{$customer->name}.pdf");
    }
   

    public function printReceipt(Order $order)
{
    $order->load('items.product', 'customer');

    $pdf = Pdf::loadView('orders.receipt', compact('order'))
              ->setPaper([0, 0, 226, 500], 'portrait'); // ukuran thermal printer

    return $pdf->stream("Struk_Order_{$order->id}.pdf"); // stream biar bisa auto print di browser
}
}
