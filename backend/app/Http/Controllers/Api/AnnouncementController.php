<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller
{
    /**
     * Public published announcements.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Announcement::query();

        // If not admin, only show published
        $user = $request->user('sanctum');
        if (!$user || !$user->isAdmin()) {
            $query->published();
        } elseif ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $announcements = $query->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => $announcements,
        ]);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return response()->json([
                'success' => false,
                'message' => 'Pengumuman tidak ditemukan.',
            ], 404);
        }

        $user = $request->user('sanctum');
        if ($announcement->status !== 'published' && (!$user || !$user->isAdmin())) {
            return response()->json([
                'success' => false,
                'message' => 'Pengumuman belum dipublikasikan.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $announcement,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:draft,published,archived',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $publishedAt = $request->status === 'published' ? now() : null;

        $announcement = Announcement::create([
            'title' => $request->title,
            'content' => $request->content,
            'status' => $request->status,
            'published_at' => $publishedAt,
            'admin_id' => $request->user()->id,
        ]);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'create_announcement',
            'model_type' => Announcement::class,
            'model_id' => $announcement->id,
            'description' => 'Membuat pengumuman: ' . $announcement->title,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil dibuat.',
            'data' => $announcement,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $announcement = Announcement::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'status' => 'sometimes|required|in:draft,published,archived',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($request->has('status') && $request->status === 'published' && !$announcement->published_at) {
            $announcement->published_at = now();
        }

        $announcement->update($request->only(['title', 'content', 'status']));

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil diperbarui.',
            'data' => $announcement,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil dihapus.',
        ]);
    }
}
