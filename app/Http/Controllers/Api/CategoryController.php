<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * 取得所有分類列表
     */
    public function index(): JsonResponse
    {
        $categories = Category::all();
        
        return response()->json([
            'success' => true,
            'data' => $categories,
            'message' => '分類列表取得成功'
        ]);
    }

    /**
     * 取得單一分類詳情
     */
    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => '分類詳情取得成功'
        ]);
    }

    /**
     * 建立新分類
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $category = Category::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => '分類建立成功'
        ], 201);
    }

    /**
     * 更新分類
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $category->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => '分類更新成功'
        ]);
    }

    /**
     * 刪除分類
     */
    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => '分類刪除成功'
        ]);
    }
} 