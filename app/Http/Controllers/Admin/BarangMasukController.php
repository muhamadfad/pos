<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mitra;
use App\Models\BarangMasuk;
use App\Models\BarangMasukItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangMasukController extends Controller
{
    // 1. Index semua transaksi barang masuk
 public function index()
{
    $barangMasuk = BarangMasuk::with(['mitra', 'items.product'])
        ->latest()
        ->paginate(15);

    return view('admin.transactions.in.index', compact('barangMasuk'));
}



    // 2. Pilih mitra
    public function pilihMitra()
    {
        $mitras = Mitra::orderBy('nama_mitra')->get();
        return view('admin.transactions.in.mitra', compact('mitras'));
    }

    // 3. Transaksi per mitra
   public function transaksiPerMitra(Request $request, $mitraId)
{
    $mitra = Mitra::findOrFail($mitraId);

    $query = BarangMasuk::where('mitra_id', $mitraId)
        ->with('items.product')
        ->orderBy('tanggal', 'desc');

    // 🔎 Search
    if ($request->filled('search')) {
        $query->whereHas('items.product', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%');
        });
    }

    /*
    ==============================
    PRIORITAS FILTER:
    1. Range tanggal (paling kuat)
    2. Dropdown cepat
    ==============================
    */

    // 📆 RANGE TANGGAL (PRIORITAS UTAMA)
    if ($request->filled('start_date') && $request->filled('end_date')) {

        $query->whereBetween('tanggal', [
            Carbon::parse($request->start_date)->startOfDay(),
            Carbon::parse($request->end_date)->endOfDay()
        ]);

    } else {

        // 📅 FILTER CEPAT
        if ($request->filter === 'today') {
            $query->whereDate('tanggal', Carbon::today());
        }

        if ($request->filter === 'month') {
            $query->whereMonth('tanggal', Carbon::now()->month)
                  ->whereYear('tanggal', Carbon::now()->year);
        }
    }

    $transactions = $query->paginate(10)->withQueryString();

    if ($request->ajax()) {
        return view('admin.transactions.in.partials.table',
            compact('transactions'))->render();
    }

    $total = BarangMasuk::where('mitra_id', $mitraId)->count();

    return view('admin.transactions.in.transaksi',
        compact('mitra', 'transactions', 'total'));
}


    // 4. Form tambah transaksi
    public function create($mitraId)
    {
        $mitra = Mitra::findOrFail($mitraId);
        $products = Product::orderBy('name')->get();

        return view('admin.transactions.in.create', compact('mitra', 'products'));
    }

    // 5. Simpan transaksi
    public function store(Request $request, $mitraId)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.harga_beli' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'dibayar' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $mitraId) {

            $total = collect($validated['items'])->sum(function ($item) {
                return $item['quantity'] * $item['harga_beli'];
            });

            $dibayar = $validated['dibayar'] ?? 0;
            $status = $dibayar >= $total ? 'LUNAS' : 'HUTANG';

            $barangMasuk = BarangMasuk::create([
                'mitra_id' => $mitraId,
                'tanggal' => $validated['tanggal'],
                'total_harga' => $total,
                'total_dibayar' => $dibayar,
                'status_pembayaran' => $status,
            ]);

            foreach ($validated['items'] as $item) {

                    $subtotal = $item['quantity'] * $item['harga_beli'];

                    BarangMasukItem::create([
                        'barang_masuk_id' => $barangMasuk->id,
                        'product_id'      => $item['product_id'],
                        'qty'             => $item['quantity'],
                        'harga_beli'      => $item['harga_beli'], // ✅ BENAR
                        'subtotal'        => $subtotal,
                    ]);

                    Product::where('id', $item['product_id'])
                        ->increment('stock', $item['quantity']);
                }

        });

        return redirect()->route('admin.transactions.in.perMitra', $mitraId)
            ->with('success', 'Transaksi berhasil disimpan!');
    }

    // 6. Detail transaksi
    public function show($id)
    {
        $transaksi = BarangMasuk::with([
    'mitra',
    'items.product',
    'pembayaran'
])->findOrFail($id);

        return view('admin.transactions.in.show', compact('transaksi'));
    }
    
    public function downloadPdf(Request $request, $mitraId)
{
    $mitra = Mitra::findOrFail($mitraId);

    $query = BarangMasuk::where('mitra_id', $mitraId)
        ->with('items.product')
        ->orderBy('tanggal', 'desc');

    // Copy logic filter yang sama seperti transaksiPerMitra

    if ($request->filled('search')) {
        $query->whereHas('items.product', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%');
        });
    }

    if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->whereBetween('tanggal', [
            Carbon::parse($request->start_date)->startOfDay(),
            Carbon::parse($request->end_date)->endOfDay()
        ]);
    } else {
        if ($request->filter === 'today') {
            $query->whereDate('tanggal', Carbon::today());
        }

        if ($request->filter === 'month') {
            $query->whereMonth('tanggal', Carbon::now()->month)
                  ->whereYear('tanggal', Carbon::now()->year);
        }
    }

    $transactions = $query->get();

    $pdf = Pdf::loadView('admin.transactions.in.pdf', [
        'mitra' => $mitra,
        'transactions' => $transactions
    ])->setPaper('a4', 'portrait');

    return $pdf->download('Laporan_Transaksi_'.$mitra->nama_mitra.'.pdf');
}
}
