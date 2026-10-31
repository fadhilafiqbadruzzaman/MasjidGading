<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Transaction;
use App\Models\TransactionAudit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Transaction::with(['category', 'creator', 'admin']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ]);
    }

    public function show($id): JsonResponse
    {
        $transaction = Transaction::with(['category', 'creator', 'admin', 'audits.user'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $transaction,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:income,expense',
            'category_id' => 'required|exists:transaction_categories,id',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'transaction_date' => 'required|date',
            'status' => 'required|in:pending,verified',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        $refNumber = 'TRX-' . date('Ym') . '-' . strtoupper(substr(uniqid(), -5));

        $transaction = DB::transaction(function () use ($request, $user, $refNumber) {
            $trx = Transaction::create([
                'type' => $request->type,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'amount' => $request->amount,
                'transaction_date' => $request->transaction_date,
                'reference_number' => $refNumber,
                'status' => $request->status,
                'created_by' => $user->id,
                'admin_id' => $user->id,
                'notes' => $request->notes,
            ]);

            TransactionAudit::create([
                'transaction_id' => $trx->id,
                'action' => 'created',
                'old_data' => null,
                'new_data' => $trx->toArray(),
                'performed_by' => $user->id,
                'reason' => 'Pencatatan transaksi baru',
            ]);

            return $trx;
        });

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dicatat.',
            'data' => $transaction->load('category'),
        ], 201);
    }

    /**
     * BR-09: Koreksi atau perubahan transaksi penting meninggalkan jejak audit.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $transaction = Transaction::findOrFail($id);
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'description' => 'sometimes|required|string',
            'amount' => 'sometimes|required|numeric|min:1',
            'transaction_date' => 'sometimes|required|date',
            'category_id' => 'sometimes|required|exists:transaction_categories,id',
            'notes' => 'nullable|string',
            'reason' => 'required|string|min:5', // Alasan koreksi wajib diisi
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal. Alasan koreksi wajib diisi.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $oldData = $transaction->toArray();

        DB::transaction(function () use ($transaction, $request, $user, $oldData) {
            $transaction->update($request->only([
                'description',
                'amount',
                'transaction_date',
                'category_id',
                'notes',
            ]));

            TransactionAudit::create([
                'transaction_id' => $transaction->id,
                'action' => 'updated',
                'old_data' => $oldData,
                'new_data' => $transaction->fresh()->toArray(),
                'performed_by' => $user->id,
                'reason' => $request->reason,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil diperbarui dan diaudit.',
            'data' => $transaction->load('category'),
        ]);
    }

    public function verify(Request $request, $id): JsonResponse
    {
        $transaction = Transaction::findOrFail($id);
        $user = $request->user();

        $oldData = $transaction->toArray();

        $transaction->status = 'verified';
        $transaction->admin_id = $user->id;
        $transaction->save();

        TransactionAudit::create([
            'transaction_id' => $transaction->id,
            'action' => 'verified',
            'old_data' => $oldData,
            'new_data' => $transaction->fresh()->toArray(),
            'performed_by' => $user->id,
            'reason' => 'Verifikasi transaksi oleh admin',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil diverifikasi.',
            'data' => $transaction,
        ]);
    }

    public function cancel(Request $request, $id): JsonResponse
    {
        $transaction = Transaction::findOrFail($id);
        $user = $request->user();

        $request->validate(['reason' => 'required|string|min:5']);

        $oldData = $transaction->toArray();

        $transaction->status = 'cancelled';
        $transaction->save();

        TransactionAudit::create([
            'transaction_id' => $transaction->id,
            'action' => 'cancelled',
            'old_data' => $oldData,
            'new_data' => $transaction->fresh()->toArray(),
            'performed_by' => $user->id,
            'reason' => $request->reason,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dibatalkan dan diaudit.',
        ]);
    }
}
