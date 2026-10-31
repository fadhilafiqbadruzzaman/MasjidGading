<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SocialApplication;
use App\Models\SocialDistribution;
use App\Models\SocialProgram;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SocialController extends Controller
{
    /**
     * Program bantuan sosial yang dipublikasikan (BR-14).
     */
    public function publicPrograms(): JsonResponse
    {
        $programs = SocialProgram::where('is_published', true)
            ->whereIn('status', ['planning', 'active', 'completed'])
            ->withCount('distributions')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $programs,
        ]);
    }

    /**
     * Jamaah mengajukan permohonan bantuan sosial.
     */
    public function apply(Request $request, $programId): JsonResponse
    {
        $program = SocialProgram::where('is_published', true)->findOrFail($programId);
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'applicant_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'reason' => 'required|string',
            'requested_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $application = SocialApplication::create([
            'program_id' => $program->id,
            'user_id' => $user?->id,
            'applicant_name' => $request->applicant_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'reason' => $request->reason,
            'requested_amount' => $request->requested_amount,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permohonan bantuan sosial berhasil diajukan dan menunggu verifikasi pengurus.',
            'data' => $application->load('program'),
        ], 201);
    }

    /**
     * Riwayat permohonan bansos oleh jamaah yang login.
     */
    public function myApplications(Request $request): JsonResponse
    {
        $user = $request->user();
        $applications = SocialApplication::where('user_id', $user->id)
            ->with(['program', 'reviewer'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $applications,
        ]);
    }

    // Admin methods - BR-13: Protected data!
    public function allApplications(Request $request): JsonResponse
    {
        $applications = SocialApplication::with(['program', 'user', 'reviewer'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $applications,
        ]);
    }

    public function applications(Request $request, $programId): JsonResponse
    {
        $applications = SocialApplication::where('program_id', $programId)
            ->with('reviewer')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $applications,
        ]);
    }

    public function reviewApplication(Request $request, $id): JsonResponse
    {
        $application = SocialApplication::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $application->update([
            'status' => $request->status,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status permohonan berhasil diperbarui.',
            'data' => $application,
        ]);
    }

    public function storeDistribution(Request $request, $programId): JsonResponse
    {
        $program = SocialProgram::findOrFail($programId);

        $validator = Validator::make($request->all(), [
            'recipient_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'distributed_at' => 'required|date',
            'description' => 'nullable|string',
            'application_id' => 'nullable|exists:social_applications,id',
            'document_path' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $distribution = SocialDistribution::create(array_merge($request->all(), [
            'program_id' => $program->id,
            'admin_id' => $request->user()->id,
        ]));

        if ($request->filled('application_id')) {
            SocialApplication::where('id', $request->application_id)->update(['status' => 'distributed']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Penyaluran bantuan sosial berhasil dicatat.',
            'data' => $distribution,
        ], 201);
    }
}
