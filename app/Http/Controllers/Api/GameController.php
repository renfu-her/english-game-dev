<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\GameRecord;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GameController extends Controller
{
    /**
     * 取得遊戲問題
     */
    public function getQuestions(Room $room): JsonResponse
    {
        $member = auth('member')->user();

        // 檢查是否在房間中
        if (!$room->players()->where('member_id', $member->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => '您不在房間中'
            ], 403);
        }

        // 檢查房間狀態
        if ($room->status !== 'playing') {
            return response()->json([
                'success' => false,
                'message' => '遊戲尚未開始'
            ], 400);
        }

        // 取得隨機問題
        $questions = Question::where('category_id', $room->category_id)
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit($room->question_count)
            ->get();

        // 移除正確答案，只返回選項
        $questions->each(function ($question) {
            $question->makeHidden(['correct_answer', 'explanation']);
        });

        return response()->json([
            'success' => true,
            'data' => [
                'questions' => $questions,
                'time_limit' => $room->time_limit,
                'question_count' => $room->question_count
            ],
            'message' => '遊戲問題取得成功'
        ]);
    }

    /**
     * 提交答案
     */
    public function submitAnswer(Request $request, Room $room): JsonResponse
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer' => 'required|string'
        ]);

        $member = auth('member')->user();

        // 檢查是否在房間中
        if (!$room->players()->where('member_id', $member->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => '您不在房間中'
            ], 403);
        }

        // 檢查房間狀態
        if ($room->status !== 'playing') {
            return response()->json([
                'success' => false,
                'message' => '遊戲尚未開始'
            ], 400);
        }

        $question = Question::find($request->question_id);
        $isCorrect = $question->correct_answer === $request->answer;

        // 記錄答案
        $gameRecord = GameRecord::create([
            'room_id' => $room->id,
            'member_id' => $member->id,
            'question_id' => $question->id,
            'answer' => $request->answer,
            'is_correct' => $isCorrect,
            'time_taken' => $request->get('time_taken', 0)
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'is_correct' => $isCorrect,
                'correct_answer' => $question->correct_answer,
                'explanation' => $question->explanation
            ],
            'message' => $isCorrect ? '答案正確！' : '答案錯誤'
        ]);
    }

    /**
     * 結束遊戲
     */
    public function endGame(Room $room): JsonResponse
    {
        $member = auth('member')->user();

        // 檢查是否在房間中
        if (!$room->players()->where('member_id', $member->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => '您不在房間中'
            ], 403);
        }

        // 檢查房間狀態
        if ($room->status !== 'playing') {
            return response()->json([
                'success' => false,
                'message' => '遊戲尚未開始'
            ], 400);
        }

        // 更新房間狀態
        $room->update(['status' => 'finished']);

        // 計算遊戲結果
        $results = $this->calculateGameResults($room);

        return response()->json([
            'success' => true,
            'data' => $results,
            'message' => '遊戲結束'
        ]);
    }

    /**
     * 取得遊戲結果
     */
    public function getGameResults(Room $room): JsonResponse
    {
        $member = auth('member')->user();

        // 檢查是否在房間中
        if (!$room->players()->where('member_id', $member->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => '您不在房間中'
            ], 403);
        }

        $results = $this->calculateGameResults($room);

        return response()->json([
            'success' => true,
            'data' => $results,
            'message' => '遊戲結果取得成功'
        ]);
    }

    /**
     * 取得我的遊戲記錄
     */
    public function myGameRecords(Request $request): JsonResponse
    {
        $member = auth('member')->user();

        $records = GameRecord::with(['room', 'question.category'])
            ->where('member_id', $member->id)
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $records,
            'message' => '遊戲記錄取得成功'
        ]);
    }

    /**
     * 取得排行榜
     */
    public function leaderboard(Request $request): JsonResponse
    {
        $query = GameRecord::with('member')
            ->selectRaw('member_id, COUNT(*) as total_questions, SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
            ->groupBy('member_id')
            ->having('total_questions', '>=', 10) // 至少答過10題
            ->orderByRaw('(SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) DESC')
            ->orderBy('correct_answers', 'desc');

        $leaderboard = $query->paginate($request->get('per_page', 20));

        // 計算準確率
        $leaderboard->getCollection()->transform(function ($item) {
            $item->accuracy = round(($item->correct_answers / $item->total_questions) * 100, 2);
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $leaderboard,
            'message' => '排行榜取得成功'
        ]);
    }

    /**
     * 取得公開的遊戲記錄（首頁用）
     */
    public function publicGameRecords(Request $request): JsonResponse
    {
        $request->validate([
            'per_page' => 'nullable|integer|min:1|max:50',
            'category_id' => 'nullable|exists:categories,id',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'member_id' => 'nullable|exists:members,id'
        ]);

        $query = GameRecord::with(['member', 'room', 'question.category'])
            ->orderBy('created_at', 'desc');

        // 根據分類篩選
        if ($request->has('category_id')) {
            $query->whereHas('question', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        // 根據難度篩選
        if ($request->has('difficulty')) {
            $query->whereHas('question', function ($q) use ($request) {
                $q->where('difficulty', $request->difficulty);
            });
        }

        // 根據會員篩選
        if ($request->has('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        $records = $query->paginate($request->get('per_page', 15));

        // 計算統計資料
        $stats = [
            'total_records' => GameRecord::count(),
            'total_correct' => GameRecord::where('is_correct', true)->count(),
            'total_members' => GameRecord::distinct('member_id')->count(),
            'total_rooms' => GameRecord::distinct('room_id')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'records' => $records,
                'stats' => $stats
            ],
            'message' => '公開遊戲記錄取得成功'
        ]);
    }

    /**
     * 取得公開的遊戲統計（首頁用）
     */
    public function publicGameStats(): JsonResponse
    {
        // 今日統計
        $today = now()->startOfDay();
        $todayRecords = GameRecord::where('created_at', '>=', $today)->count();
        $todayCorrect = GameRecord::where('created_at', '>=', $today)
            ->where('is_correct', true)->count();

        // 本週統計
        $weekStart = now()->startOfWeek();
        $weekRecords = GameRecord::where('created_at', '>=', $weekStart)->count();
        $weekCorrect = GameRecord::where('created_at', '>=', $weekStart)
            ->where('is_correct', true)->count();

        // 本月統計
        $monthStart = now()->startOfMonth();
        $monthRecords = GameRecord::where('created_at', '>=', $monthStart)->count();
        $monthCorrect = GameRecord::where('created_at', '>=', $monthStart)
            ->where('is_correct', true)->count();

        // 總體統計
        $totalRecords = GameRecord::count();
        $totalCorrect = GameRecord::where('is_correct', true)->count();
        $totalMembers = GameRecord::distinct('member_id')->count();
        $totalRooms = GameRecord::distinct('room_id')->count();

        // 熱門分類
        $popularCategories = GameRecord::with('question.category')
            ->selectRaw('questions.category_id, COUNT(*) as count')
            ->join('questions', 'game_records.question_id', '=', 'questions.id')
            ->groupBy('questions.category_id')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->question->category,
                    'count' => $item->count
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'today' => [
                    'total' => $todayRecords,
                    'correct' => $todayCorrect,
                    'accuracy' => $todayRecords > 0 ? round(($todayCorrect / $todayRecords) * 100, 2) : 0
                ],
                'week' => [
                    'total' => $weekRecords,
                    'correct' => $weekCorrect,
                    'accuracy' => $weekRecords > 0 ? round(($weekCorrect / $weekRecords) * 100, 2) : 0
                ],
                'month' => [
                    'total' => $monthRecords,
                    'correct' => $monthCorrect,
                    'accuracy' => $monthRecords > 0 ? round(($monthCorrect / $monthRecords) * 100, 2) : 0
                ],
                'total' => [
                    'records' => $totalRecords,
                    'correct' => $totalCorrect,
                    'accuracy' => $totalRecords > 0 ? round(($totalCorrect / $totalRecords) * 100, 2) : 0,
                    'members' => $totalMembers,
                    'rooms' => $totalRooms
                ],
                'popular_categories' => $popularCategories
            ],
            'message' => '遊戲統計取得成功'
        ]);
    }

    /**
     * 計算遊戲結果
     */
    private function calculateGameResults(Room $room): array
    {
        $players = $room->players()->with('member')->get();
        $results = [];

        foreach ($players as $player) {
            $records = GameRecord::where('room_id', $room->id)
                ->where('member_id', $player->member_id)
                ->get();

            $totalQuestions = $records->count();
            $correctAnswers = $records->where('is_correct', true)->count();
            $accuracy = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0;
            $averageTime = $records->avg('time_taken');

            $results[] = [
                'member' => $player->member,
                'total_questions' => $totalQuestions,
                'correct_answers' => $correctAnswers,
                'accuracy' => $accuracy,
                'average_time' => round($averageTime, 2),
                'score' => $correctAnswers * 10 + max(0, 100 - $averageTime) // 簡單計分規則
            ];
        }

        // 按分數排序
        usort($results, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return [
            'room' => $room,
            'results' => $results,
            'winner' => $results[0] ?? null
        ];
    }
} 