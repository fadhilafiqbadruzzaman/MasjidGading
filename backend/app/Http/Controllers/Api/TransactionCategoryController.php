<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TransactionCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = TransactionCategory::orderBy('type')->orderBy('name')->get();
        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:transaction_categories',
            'type' => 'required|in:income,expense',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $category = TransactionCategory::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Kategori transaksi berhasil dibuat.',
            'data' => $category,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $category = TransactionCategory::findOrFail($id);
        $category->update($request->only(['name', 'type', 'description']));

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diperbarui.',
            'data' => $category,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $category = TransactionCategory::findOrFail($id);

        if ($category->transactions()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak dapat dihapus karena masih digunakan pada transaksi.',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus.',
        ]);
    }
}
