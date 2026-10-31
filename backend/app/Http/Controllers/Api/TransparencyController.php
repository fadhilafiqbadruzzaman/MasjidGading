<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProgramAllocation;
use App\Models\ProgramRealization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransparencyController extends Controller
{
    /**
     * BR-14: Jejak Amanah - Publik hanya melihat alokasi & realisasi yang sudah berstatus is_published.
     */
    public function publicPrograms(Request $request): JsonResponse
    {
        $programs = ProgramAllocation::where('is_published', true)
            ->with(['campaign', 'realizations'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => $item->description,
                    'total_allocated' => (float)$item->total_allocated,
                    'total_realized' => $item->total_realized,
                    'remaining_balance' => $item->remaining_balance,
                    'campaign_title' => $item->campaign ? $item->campaign->title : null,
                    'status' => $item->status,
                    'published_at' => $item->published_at ? $item->published_at->format('Y-m-d H:i') : null,
                    'realizations_count' => $item->realizations->count(),
                    'realizations' => $item->realizations,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $programs,
        ]);
    }

    public function show($id): JsonResponse
    {
        $program = ProgramAllocation::with(['campaign', 'realizations'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $program->id,
                'title' => $program->title,
                'description' => $program->description,
                'total_allocated' => (float)$program->total_allocated,
                'total_realized' => $program->total_realized,
                'remaining_balance' => $program->remaining_balance,
                'campaign_title' => $program->campaign ? $program->campaign->title : null,
                'status' => $program->status,
                'is_published' => $program->is_published,
                'realizations' => $program->realizations,
            ],
        ]);
    }

    // Admin methods
    public function storeAllocation(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'total_allocated' => 'required|numeric|min:1',
            'campaign_id' => 'nullable|exists:donation_campaigns,id',
            'status' => 'required|in:draft,active,completed',
            'is_published' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $allocation = ProgramAllocation::create(array_merge($request->all(), [
            'admin_id' => $request->user()->id,
            'published_at' => $request->is_published ? now() : null,
            'published_by' => $request->is_published ? $request->user()->id : null,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Alokasi program Jejak Amanah berhasil dibuat.',
            'data' => $allocation,
        ], 201);
    }

    public function storeRealization(Request $request, $allocationId): JsonResponse
    {
        $allocation = ProgramAllocation::findOrFail($allocationId);

        $validator = Validator::make($request->all(), [
            'description' => 'required|string',
            'amount_used' => 'required|numeric|min:1',
            'realization_date' => 'required|date',
            'document_path' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $realization = ProgramRealization::create([
            'allocation_id' => $allocation->id,
            'description' => $request->description,
            'amount_used' => $request->amount_used,
            'realization_date' => $request->realization_date,
            'document_path' => $request->document_path,
            'notes' => $request->notes,
            'admin_id' => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Realisasi dana program berhasil dicatat.',
            'data' => $realization,
        ], 201);
    }
}
