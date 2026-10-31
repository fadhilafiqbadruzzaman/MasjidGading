<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ZakatDistribution;
use App\Models\ZakatRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ZakatController extends Controller
{
    /**
     * Ringkasan publik: Jumlah total penerimaan & penyaluran per jenis tanpa mempublikasikan nama pribadi.
     */
    public function publicSummary(Request $request): JsonResponse
    {
        $year = $request->get('year', date('Y'));

        $totalFitrah = (float)ZakatRecord::where('period_year', $year)->where('type', 'fitrah')->sum('amount');
        $totalMaal = (float)ZakatRecord::where('period_year', $year)->where('type', 'maal')->sum('amount');
        $totalProfesi = (float)ZakatRecord::where('period_year', $year)->where('type', 'profesi')->sum('amount');

        $totalDistributed = (float)ZakatDistribution::whereYear('distributed_at', $year)->sum('amount');

        return response()->json([
            'success' => true,
            'data' => [
                'year' => (int)$year,
                'total_fitrah' => $totalFitrah,
                'total_maal' => $totalMaal,
                'total_profesi' => $totalProfesi,
                'total_collected' => $totalFitrah + $totalMaal + $totalProfesi,
                'total_distributed' => $totalDistributed,
                'remaining' => ($totalFitrah + $totalMaal + $totalProfesi) - $totalDistributed,
            ],
        ]);
    }

    public function records(Request $request): JsonResponse
    {
        $query = ZakatRecord::with('admin');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('year')) {
            $query->where('period_year', $request->year);
        }

        $records = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $records,
        ]);
    }

    public function storeRecord(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:fitrah,maal,profesi,fidyah,other',
            'period_year' => 'required|integer|min:2020',
            'period_label' => 'required|string|max:100',
            'amount' => 'required|numeric|min:1',
            'payer_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $record = ZakatRecord::create(array_merge($request->all(), [
            'admin_id' => $request->user()->id,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Pencatatan zakat berhasil disimpan.',
            'data' => $record,
        ], 201);
    }

    public function distributions(Request $request): JsonResponse
    {
        $distributions = ZakatDistribution::with('zakatRecord')
            ->orderBy('distributed_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $distributions,
        ]);
    }

    public function storeDistribution(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:fitrah,maal,profesi,fidyah,other',
            'recipient_name' => 'required|string|max:255',
            'recipient_category' => 'required|in:fakir,miskin,amil,mualaf,riqab,gharim,fisabilillah,ibnu_sabil',
            'amount' => 'required|numeric|min:1',
            'distributed_at' => 'required|date',
            'notes' => 'nullable|string',
            'zakat_record_id' => 'nullable|exists:zakat_records,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $distribution = ZakatDistribution::create(array_merge($request->all(), [
            'admin_id' => $request->user()->id,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Penyaluran zakat berhasil dicatat.',
            'data' => $distribution,
        ], 201);
    }
}
