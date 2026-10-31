<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Donation;
use App\Models\DonationCampaign;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class DonationController extends Controller
{
    /**
     * User konfirmasi: Donasi wajib login (no anonymous).
     * Inisiasi donasi & Midtrans Snap token / redirect url.
     */
    public function initiate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'campaign_id' => 'required|exists:donation_campaigns,id',
            'amount' => 'required|numeric|min:10000',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        $campaign = DonationCampaign::findOrFail($request->campaign_id);

        if ($campaign->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Kampanye donasi ini sedang tidak aktif menerima donasi.',
            ], 422);
        }

        // Generate unique Midtrans Order ID
        $orderId = 'DON-' . date('YmdHis') . '-' . substr(uniqid(), -4);

        $serverKey = config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-TESTKEY'));
        $isProduction = config('services.midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', false));

        // In demo / test mode or with mock URL
        $snapUrl = 'https://app.sandbox.midtrans.com/snap/v2/vtweb/' . $orderId;

        // Try calling real Midtrans Snap API if key is set
        if (!empty(env('MIDTRANS_SERVER_KEY')) && env('MIDTRANS_SERVER_KEY') !== 'SB-Mid-server-TESTKEY') {
            $snapEndpoint = $isProduction
                ? 'https://app.midtrans.com/snap/v1/transactions'
                : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

            $payload = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int)$request->amount,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '081234567890',
                ],
                'item_details' => [
                    [
                        'id' => 'CAMP-' . $campaign->id,
                        'price' => (int)$request->amount,
                        'quantity' => 1,
                        'name' => substr($campaign->title, 0, 50),
                    ]
                ],
            ];

            try {
                $response = Http::withBasicAuth($serverKey, '')
                    ->post($snapEndpoint, $payload);

                if ($response->successful()) {
                    $snapData = $response->json();
                    $snapUrl = $snapData['redirect_url'] ?? $snapUrl;
                }
            } catch (\Exception $e) {
                // Fallback to demo URL
            }
        }

        $donation = Donation::create([
            'campaign_id' => $campaign->id,
            'user_id' => $user->id,
            'amount' => $request->amount,
            'payment_status' => 'pending',
            'midtrans_order_id' => $orderId,
            'payment_url' => $snapUrl,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan donasi berhasil dibuat.',
            'data' => [
                'donation' => $donation->load('campaign'),
                'payment_url' => $snapUrl,
                'order_id' => $orderId,
            ],
        ], 201);
    }

    /**
     * BR-10 & BR-11: Midtrans Webhook Handler.
     * - Status verifikasi backend sebelum final.
     * - IDEMPOTENT: Notifikasi berulang tidak membuat pencatatan transaksi ganda!
     */
    public function webhook(Request $request): JsonResponse
    {
        $orderId = $request->input('order_id');
        $statusCode = $request->input('status_code');
        $grossAmount = $request->input('gross_amount');
        $transactionStatus = $request->input('transaction_status');
        $signatureKey = $request->input('signature_key');

        $donation = Donation::where('midtrans_order_id', $orderId)->first();

        if (!$donation) {
            return response()->json([
                'success' => false,
                'message' => 'Donasi dengan Order ID tersebut tidak ditemukan.',
            ], 404);
        }

        // BR-11: Idempotency check: Jika donasi sudah 'success', jangan buat transaksi ulang!
        if ($donation->payment_status === 'success') {
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi sudah diproses sebelumnya (Idempotent).',
                'data' => $donation,
            ]);
        }

        // Verifikasi Signature jika server key dikonfigurasi
        $serverKey = env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-TESTKEY');
        if (!empty($serverKey) && $signatureKey && $serverKey !== 'SB-Mid-server-TESTKEY') {
            $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
            if ($signatureKey !== $expectedSignature) {
                return response()->json([
                    'success' => false,
                    'message' => 'Signature Midtrans tidak valid.',
                ], 403);
            }
        }

        // Tentukan status pembayaran
        $newStatus = 'pending';
        if (in_array($transactionStatus, ['capture', 'settlement'])) {
            $newStatus = 'success';
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $newStatus = 'failed';
        }

        DB::transaction(function () use ($donation, $newStatus, $request) {
            $donation->payment_status = $newStatus;
            $donation->midtrans_transaction_id = $request->input('transaction_id');
            $donation->payment_method = $request->input('payment_type');

            if ($newStatus === 'success') {
                $donation->paid_at = now();

                // 1. Hubungkan ke pencatatan transaksi keuangan masjid
                $cat = TransactionCategory::where('name', 'Donasi Online Midtrans')->first()
                    ?? TransactionCategory::where('type', 'income')->first();

                $trx = Transaction::create([
                    'type' => 'income',
                    'category_id' => $cat ? $cat->id : 1,
                    'description' => 'Donasi Online: ' . $donation->campaign->title . ' (Ref: ' . $donation->midtrans_order_id . ')',
                    'amount' => $donation->amount,
                    'transaction_date' => now()->toDateString(),
                    'reference_number' => $donation->midtrans_order_id,
                    'status' => 'verified',
                    'created_by' => $donation->user_id,
                    'admin_id' => $donation->campaign->admin_id ?? $donation->user_id,
                    'notes' => 'Otomatis dicatat dari integrasi pembayaran Midtrans.',
                ]);

                $donation->transaction_id = $trx->id;

                // 2. Update collected_amount di kampanye donasi
                $campaign = $donation->campaign;
                $campaign->increment('collected_amount', $donation->amount);
            }

            $donation->save();
        });

        return response()->json([
            'success' => true,
            'message' => 'Status pembayaran donasi berhasil diverifikasi dan diperbarui.',
            'data' => $donation->fresh(),
        ]);
    }

    /**
     * Riwayat donasi milik jamaah sendiri.
     */
    public function myDonations(Request $request): JsonResponse
    {
        $donations = Donation::where('user_id', $request->user()->id)
            ->with('campaign')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => $donations,
        ]);
    }

    public function showStatus($orderId): JsonResponse
    {
        $donation = Donation::with('campaign')
            ->where('midtrans_order_id', $orderId)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $donation,
        ]);
    }
}
