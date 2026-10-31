<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DonationCampaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DonationCampaignController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DonationCampaign::query();

        $user = $request->user('sanctum');
        if (!$user || !$user->isAdmin()) {
            $query->where('status', 'active');
        } elseif ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $campaigns = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 10));

        // Append progress percentage
        $campaigns->getCollection()->transform(function ($campaign) {
            $campaign->progress_percentage = $campaign->progress_percentage;
            return $campaign;
        });

        return response()->json([
            'success' => true,
            'data' => $campaigns,
        ]);
    }

    public function show($id): JsonResponse
    {
        $campaign = DonationCampaign::with(['programAllocations.realizations'])->findOrFail($id);
        $campaign->progress_percentage = $campaign->progress_percentage;

        return response()->json([
            'success' => true,
            'data' => $campaign,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'target_amount' => 'nullable|numeric|min:1',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,paused,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $campaign = DonationCampaign::create(array_merge($request->all(), [
            'admin_id' => $request->user()->id,
            'collected_amount' => 0,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Kampanye donasi berhasil dibuat.',
            'data' => $campaign,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $campaign = DonationCampaign::findOrFail($id);
        $campaign->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Kampanye donasi berhasil diperbarui.',
            'data' => $campaign,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $campaign = DonationCampaign::findOrFail($id);
        $campaign->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kampanye donasi berhasil dihapus.',
        ]);
    }
}
