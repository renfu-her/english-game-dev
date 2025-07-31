<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class QuestionController extends Controller
{
    /**
     * 取得問題列表
     */
    public function index(Request $request): JsonResponse
    {
        $query = Question::with('category');

        // 根據分類篩選
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 根據難度篩選
        if ($request->has('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        // 只顯示啟用的問題
        $query->where('is_active', true);

        $questions = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $questions,
            'message' => '問題列表取得成功'
        ]);
    }

    /**
     * 取得單一問題詳情
     */
    public function show(Question $question): JsonResponse
    {
        $question->load('category');

        return response()->json([
            'success' => true,
            'data' => $question,
            'message' => '問題詳情取得成功'
        ]);
    }

    /**
     * 建立新問題
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'question' => 'required|string',
            'correct_answer' => 'required|string',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'explanation' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $question = Question::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $question->load('category'),
            'message' => '問題建立成功'
        ], 201);
    }

    /**
     * 更新問題
     */
    public function update(Request $request, Question $question): JsonResponse
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'question' => 'required|string',
            'correct_answer' => 'required|string',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'explanation' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $question->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $question->load('category'),
            'message' => '問題更新成功'
        ]);
    }

    /**
     * 刪除問題
     */
    public function destroy(Question $question): JsonResponse
    {
        $question->delete();

        return response()->json([
            'success' => true,
            'message' => '問題刪除成功'
        ]);
    }

    /**
     * 取得隨機問題
     */
    public function random(Request $request): JsonResponse
    {
        $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'count' => 'nullable|integer|min:1|max:50'
        ]);

        $query = Question::with('category')->where('is_active', true);

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        $count = $request->get('count', 10);
        $questions = $query->inRandomOrder()->limit($count)->get();

        return response()->json([
            'success' => true,
            'data' => $questions,
            'message' => '隨機問題取得成功'
        ]);
    }
} 