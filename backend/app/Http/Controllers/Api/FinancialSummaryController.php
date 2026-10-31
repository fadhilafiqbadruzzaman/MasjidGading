<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FinancialSummary;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FinancialSummaryController extends Controller
{
    /**
     * BR-07 & BR-14: Jamaah / publik hanya dapat melihat ringkasan keuangan yang telah disetujui & dipublikasikan.
     */
    public function publishedSummary(Request $request): JsonResponse
    {
        $summaries = FinancialSummary::where('is_published', true)
            ->orderBy('period_end', 'desc')
            ->get()
            ->map(function ($summary) {
                return $summary->calculateReport('all');
            });

        return response()->json([
            'success' => true,
            'data' => $summaries,
        ]);
    }

    public function index(): JsonResponse
    {
        $summaries = FinancialSummary::with('publisher')
            ->orderBy('period_end', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $summaries,
        ]);
    }

    public function show($id): JsonResponse
    {
        $summary = FinancialSummary::findOrFail($id);
        $report = $summary->calculateReport('all');

        return response()->json([
            'success' => true,
            'data' => array_merge($summary->toArray(), ['report' => $report]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'period_label' => 'required|string|max:100',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'opening_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $summary = FinancialSummary::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Ringkasan periode keuangan berhasil dibuat.',
            'data' => $summary,
        ], 201);
    }

    public function publish(Request $request, $id): JsonResponse
    {
        $summary = FinancialSummary::findOrFail($id);
        $user = $request->user();

        $summary->is_published = true;
        $summary->published_by = $user->id;
        $summary->published_at = now();
        $summary->save();

        return response()->json([
            'success' => true,
            'message' => 'Ringkasan keuangan berhasil dipublikasikan untuk jamaah.',
            'data' => $summary,
        ]);
    }

    public function unpublish($id): JsonResponse
    {
        $summary = FinancialSummary::findOrFail($id);
        $summary->is_published = false;
        $summary->save();

        return response()->json([
            'success' => true,
            'message' => 'Publikasi ringkasan keuangan dibatalkan.',
            'data' => $summary,
        ]);
    }
}
