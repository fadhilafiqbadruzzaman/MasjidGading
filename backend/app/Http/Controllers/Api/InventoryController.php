<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Inventory::query();

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $inventories = $query->orderBy('name', 'asc')->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $inventories,
        ]);
    }

    public function show($id): JsonResponse
    {
        $inventory = Inventory::with(['maintenanceTasks.marbot'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $inventory,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'quantity' => 'required|integer|min:1',
            'location' => 'nullable|string|max:255',
            'condition' => 'required|in:good,fair,needs_repair,damaged',
            'last_maintained_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $inventory = Inventory::create(array_merge($request->all(), [
            'admin_id' => $request->user()->id,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Data inventaris berhasil ditambahkan.',
            'data' => $inventory,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $inventory = Inventory::findOrFail($id);
        $inventory->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data inventaris berhasil diperbarui.',
            'data' => $inventory,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $inventory = Inventory::findOrFail($id);
        $inventory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data inventaris berhasil dihapus.',
        ]);
    }
}
