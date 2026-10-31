<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Event::query();

        $user = $request->user('sanctum');
        if (!$user || !$user->isAdmin()) {
            $query->whereIn('status', ['planned', 'ongoing']);
        } elseif ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $events = $query->withCount(['registrations as active_registrations_count' => function ($q) {
            $q->where('status', 'registered');
        }])->orderBy('event_date', 'asc')->paginate($request->get('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => $events,
        ]);
    }

    public function show($id): JsonResponse
    {
        $event = Event::withCount(['registrations as active_registrations_count' => function ($q) {
            $q->where('status', 'registered');
        }])->find($id);

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Kegiatan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $event,
        ]);
    }

    /**
     * AC-04 / PRD: Jamaah mendaftar kegiatan.
     * Prevents duplicate registration & checks capacity.
     */
    public function register(Request $request, $id): JsonResponse
    {
        $event = Event::findOrFail($id);
        $user = $request->user();

        if (!$event->is_registration_open || !in_array($event->status, ['planned', 'ongoing'])) {
            return response()->json([
                'success' => false,
                'message' => 'Pendaftaran untuk kegiatan ini sedang ditutup.',
            ], 422);
        }

        // Check if full
        if ($event->isFull()) {
            return response()->json([
                'success' => false,
                'message' => 'Kapasitas pendaftaran kegiatan sudah penuh.',
            ], 422);
        }

        // Check duplicate
        $existing = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing && $existing->status === 'registered') {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah terdaftar pada kegiatan ini.',
            ], 422);
        }

        if ($existing) {
            $existing->update([
                'status' => 'registered',
                'registered_at' => now(),
                'notes' => $request->notes,
            ]);
            $registration = $existing;
        } else {
            $registration = EventRegistration::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'status' => 'registered',
                'registered_at' => now(),
                'notes' => $request->notes,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendaftar kegiatan.',
            'data' => $registration,
        ], 201);
    }

    public function cancelRegistration(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $registration = EventRegistration::where('event_id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$registration || $registration->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Pendaftaran tidak ditemukan atau sudah dibatalkan.',
            ], 404);
        }

        $registration->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran kegiatan berhasil dibatalkan.',
        ]);
    }

    public function myRegistrations(Request $request): JsonResponse
    {
        $registrations = EventRegistration::where('user_id', $request->user()->id)
            ->with('event')
            ->orderBy('registered_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $registrations,
        ]);
    }

    // Admin methods
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'event_date' => 'required|date',
            'event_time' => 'required|string',
            'capacity' => 'nullable|integer|min:1',
            'is_registration_open' => 'boolean',
            'status' => 'required|in:planned,ongoing,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $event = Event::create(array_merge($request->all(), [
            'admin_id' => $request->user()->id,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Kegiatan berhasil dibuat.',
            'data' => $event,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $event = Event::findOrFail($id);
        $event->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Kegiatan berhasil diperbarui.',
            'data' => $event,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kegiatan berhasil dihapus.',
        ]);
    }

    public function participants($id): JsonResponse
    {
        $event = Event::findOrFail($id);
        $participants = $event->registrations()->with('user')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'event' => $event,
                'participants' => $participants,
            ],
        ]);
    }
}
