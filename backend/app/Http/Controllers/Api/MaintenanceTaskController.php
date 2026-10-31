<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\MaintenanceTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaintenanceTaskController extends Controller
{
    /**
     * BR-12: Marbot hanya dapat melihat tugas yang ditugaskan kepada dirinya.
     */
    public function myTasks(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = MaintenanceTask::where('assigned_to', $user->id)
            ->with(['inventory', 'assignedBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tasks = $query->orderBy('due_date', 'asc')
            ->orderBy('priority', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tasks,
        ]);
    }

    /**
     * Marbot memperbarui status tugas pemeliharaan.
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $task = MaintenanceTask::findOrFail($id);
        $user = $request->user();

        // Validasi akses: Marbot hanya bisa update tugas miliknya sendiri, Admin bisa update semua
        if ($user->isMarbot() && $task->assigned_to !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk memperbarui tugas ini.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $task->status = $request->status;

        if ($request->status === 'in_progress' && !$task->started_at) {
            $task->started_at = now();
        } elseif ($request->status === 'completed') {
            $task->completed_at = now();
        }

        if ($request->filled('notes')) {
            $task->description = $task->description . "\n\n[Catatan Marbot (" . now()->format('d/m/Y H:i') . ")]: " . $request->notes;
        }

        $task->save();

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'update_task_status',
            'model_type' => MaintenanceTask::class,
            'model_id' => $task->id,
            'description' => $user->name . ' memperbarui status tugas #' . $task->id . ' menjadi ' . $task->status,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status tugas berhasil diperbarui.',
            'data' => $task->fresh(['inventory', 'marbot']),
        ]);
    }

    /**
     * Admin: Semua tugas pemeliharaan.
     */
    public function allTasks(Request $request): JsonResponse
    {
        $query = MaintenanceTask::with(['inventory', 'marbot', 'assignedBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('marbot_id')) {
            $query->where('assigned_to', $request->marbot_id);
        }

        $tasks = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $tasks,
        ]);
    }

    /**
     * Admin: Menugaskan pekerjaan ke Marbot.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'nullable|date',
            'inventory_id' => 'nullable|exists:inventories,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $task = MaintenanceTask::create(array_merge($request->all(), [
            'assigned_by' => $request->user()->id,
            'status' => 'pending',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Tugas pemeliharaan berhasil ditugaskan ke Marbot.',
            'data' => $task->load(['marbot', 'inventory']),
        ], 201);
    }

    public function destroy($id): JsonResponse
    {
        $task = MaintenanceTask::findOrFail($id);
        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil dihapus.',
        ]);
    }
}
