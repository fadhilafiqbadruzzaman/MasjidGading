<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FacilityReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FacilityReportController extends Controller
{
    /**
     * Marbot mengirim laporan kerusakan / kondisi fasilitas masjid.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'condition' => 'required|in:good,fair,needs_repair,damaged',
            'task_id' => 'nullable|exists:maintenance_tasks,id',
            'image_path' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $report = FacilityReport::create(array_merge($request->all(), [
            'reported_by' => $request->user()->id,
            'status' => 'open',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Laporan kondisi fasilitas berhasil dikirim ke pengurus.',
            'data' => $report,
        ], 201);
    }

    /**
     * Marbot melihat riwayat laporan yang dibuatnya.
     */
    public function myReports(Request $request): JsonResponse
    {
        $reports = FacilityReport::where('reported_by', $request->user()->id)
            ->with('task')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reports,
        ]);
    }

    /**
     * Admin melihat semua laporan dari marbot.
     */
    public function allReports(Request $request): JsonResponse
    {
        $query = FacilityReport::with(['marbot', 'task', 'acknowledgedBy', 'resolvedBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reports,
        ]);
    }

    public function acknowledge(Request $request, $id): JsonResponse
    {
        $report = FacilityReport::findOrFail($id);
        $report->status = 'acknowledged';
        $report->acknowledged_by = $request->user()->id;
        $report->save();

        return response()->json([
            'success' => true,
            'message' => 'Laporan telah diakui oleh Admin.',
            'data' => $report,
        ]);
    }

    public function resolve(Request $request, $id): JsonResponse
    {
        $report = FacilityReport::findOrFail($id);
        $report->status = 'resolved';
        $report->resolved_by = $request->user()->id;
        if ($request->filled('notes')) {
            $report->notes = $request->notes;
        }
        $report->save();

        return response()->json([
            'success' => true,
            'message' => 'Laporan fasilitas telah ditandai selesai/diperbaiki.',
            'data' => $report,
        ]);
    }
}
