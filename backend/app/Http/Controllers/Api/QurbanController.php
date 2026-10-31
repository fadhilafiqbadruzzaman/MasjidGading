<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QurbanDistribution;
use App\Models\QurbanParticipant;
use App\Models\QurbanProgram;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QurbanController extends Controller
{
    public function publicProgram(Request $request): JsonResponse
    {
        $programs = QurbanProgram::where('is_published', true)
            ->withCount(['participants', 'distributions'])
            ->orderBy('year', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $programs,
        ]);
    }

    public function show($id): JsonResponse
    {
        $program = QurbanProgram::withCount(['participants', 'distributions'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $program,
        ]);
    }

    public function storeProgram(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'year' => 'required|integer|min:2020',
            'description' => 'nullable|string',
            'status' => 'required|in:open,closed,distributed,completed',
            'is_published' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $program = QurbanProgram::create(array_merge($request->all(), [
            'admin_id' => $request->user()->id,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Program qurban berhasil dibuat.',
            'data' => $program,
        ], 201);
    }

    public function participants($programId): JsonResponse
    {
        $participants = QurbanParticipant::where('program_id', $programId)->get();

        return response()->json([
            'success' => true,
            'data' => $participants,
        ]);
    }

    public function storeParticipant(Request $request, $programId): JsonResponse
    {
        $program = QurbanProgram::findOrFail($programId);

        $validator = Validator::make($request->all(), [
            'participant_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'animal_type' => 'required|in:cow,goat,sheep,camel',
            'shares' => 'required|integer|min:1',
            'amount_paid' => 'required|numeric|min:0',
            'payment_status' => 'required|in:pending,paid',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $participant = QurbanParticipant::create(array_merge($request->all(), [
            'program_id' => $program->id,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Peserta qurban berhasil didaftarkan.',
            'data' => $participant,
        ], 201);
    }

    public function distributions($programId): JsonResponse
    {
        $distributions = QurbanDistribution::where('program_id', $programId)->get();

        return response()->json([
            'success' => true,
            'data' => $distributions,
        ]);
    }

    public function storeDistribution(Request $request, $programId): JsonResponse
    {
        $program = QurbanProgram::findOrFail($programId);

        $validator = Validator::make($request->all(), [
            'recipient_name' => 'required|string|max:255',
            'recipient_address' => 'nullable|string',
            'package_count' => 'required|integer|min:1',
            'amount_kg' => 'nullable|numeric|min:0',
            'distributed_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $distribution = QurbanDistribution::create(array_merge($request->all(), [
            'program_id' => $program->id,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Penyaluran daging qurban berhasil dicatat.',
            'data' => $distribution,
        ], 201);
    }
}
