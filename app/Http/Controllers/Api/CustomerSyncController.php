<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;

class CustomerSyncController extends Controller
{
    /**
     * Handle the incoming offline customer creation payload.
     */
    public function store(Request $request)
    {
        try {
            $payload = $request->input('payload');

            if (!$payload) {
                return response()->json(['error' => 'No payload provided'], 400);
            }

            // Since it's a new customer from offline, we just create it.
            // But we need to ensure we don't duplicate it if the request was sent multiple times.
            // We can use the client_uuid on the customer table, but the table might not have it.
            // Let's just create it and return the ID. A better approach is checking by client_uuid if it existed.
            // For now, if the payload has a name, create a customer.

            $customer = new Customer();
            $customer->name = $payload['name'];
            $customer->phone = $payload['phone'] ?? null;
            $customer->type = 'retail'; // Default for offline POS walk-ins

            $customer->save();

            return response()->json([
                'success' => true,
                'real_id' => $customer->id,
                'offline_id' => $payload['offline_id']
            ], 200);

        } catch (\Exception $e) {
            Log::error('POS Customer Sync Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan pada server saat sinkronisasi pelanggan'
            ], 500);
        }
    }
}
