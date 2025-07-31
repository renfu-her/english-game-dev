<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * 取得個人資料
     */
    public function show(): JsonResponse
    {
        $member = auth('member')->user();

        return response()->json([
            'success' => true,
            'data' => $member,
            'message' => '個人資料取得成功'
        ]);
    }

    /**
     * 更新個人資料
     */
    public function update(Request $request): JsonResponse
    {
        $member = auth('member')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('members')->ignore($member->id)
            ],
            'avatar' => 'nullable|string',
            'bio' => 'nullable|string|max:500'
        ]);

        $member->update($request->only(['name', 'email', 'avatar', 'bio']));

        return response()->json([
            'success' => true,
            'data' => $member,
            'message' => '個人資料更新成功'
        ]);
    }

    /**
     * 變更密碼
     */
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $member = auth('member')->user();

        // 驗證當前密碼
        if (!Hash::check($request->current_password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => '當前密碼錯誤'
            ], 400);
        }

        // 更新密碼
        $member->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => '密碼變更成功'
        ]);
    }

    /**
     * 取得我的統計資料
     */
    public function statistics(): JsonResponse
    {
        $member = auth('member')->user();

        // 計算遊戲統計
        $totalGames = \App\Models\GameRecord::where('member_id', $member->id)
            ->distinct('room_id')
            ->count('room_id');

        $totalQuestions = \App\Models\GameRecord::where('member_id', $member->id)->count();
        $correctAnswers = \App\Models\GameRecord::where('member_id', $member->id)
            ->where('is_correct', true)
            ->count();

        $accuracy = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0;

        // 計算平均答題時間
        $averageTime = \App\Models\GameRecord::where('member_id', $member->id)
            ->whereNotNull('time_taken')
            ->avg('time_taken');

        // 取得最佳成績
        $bestScore = \App\Models\GameRecord::where('member_id', $member->id)
            ->where('is_correct', true)
            ->count();

        // 取得最近遊戲記錄
        $recentGames = \App\Models\GameRecord::with(['room', 'question.category'])
            ->where('member_id', $member->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_games' => $totalGames,
                'total_questions' => $totalQuestions,
                'correct_answers' => $correctAnswers,
                'accuracy' => $accuracy,
                'average_time' => round($averageTime, 2),
                'best_score' => $bestScore,
                'recent_games' => $recentGames
            ],
            'message' => '統計資料取得成功'
        ]);
    }

    /**
     * 取得我的成就
     */
    public function achievements(): JsonResponse
    {
        $member = auth('member')->user();

        $achievements = [];

        // 計算各種成就
        $totalQuestions = \App\Models\GameRecord::where('member_id', $member->id)->count();
        $correctAnswers = \App\Models\GameRecord::where('member_id', $member->id)
            ->where('is_correct', true)
            ->count();

        // 答題數量成就
        if ($totalQuestions >= 100) {
            $achievements[] = [
                'name' => '答題大師',
                'description' => '已答過100題',
                'icon' => '🎯',
                'unlocked' => true
            ];
        } elseif ($totalQuestions >= 50) {
            $achievements[] = [
                'name' => '答題專家',
                'description' => '已答過50題',
                'icon' => '🎯',
                'unlocked' => true
            ];
        } elseif ($totalQuestions >= 10) {
            $achievements[] = [
                'name' => '答題新手',
                'description' => '已答過10題',
                'icon' => '🎯',
                'unlocked' => true
            ];
        }

        // 準確率成就
        if ($totalQuestions >= 10) {
            $accuracy = ($correctAnswers / $totalQuestions) * 100;
            
            if ($accuracy >= 90) {
                $achievements[] = [
                    'name' => '完美主義者',
                    'description' => '準確率達到90%',
                    'icon' => '🏆',
                    'unlocked' => true
                ];
            } elseif ($accuracy >= 80) {
                $achievements[] = [
                    'name' => '優秀學生',
                    'description' => '準確率達到80%',
                    'icon' => '🥇',
                    'unlocked' => true
                ];
            } elseif ($accuracy >= 70) {
                $achievements[] = [
                    'name' => '良好表現',
                    'description' => '準確率達到70%',
                    'icon' => '🥈',
                    'unlocked' => true
                ];
            }
        }

        // 連續答對成就
        $consecutiveCorrect = \App\Models\GameRecord::where('member_id', $member->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($record) {
                return $record->created_at->format('Y-m-d');
            })
            ->map(function ($dayRecords) {
                return $dayRecords->where('is_correct', true)->count();
            })
            ->max();

        if ($consecutiveCorrect >= 10) {
            $achievements[] = [
                'name' => '連勝王者',
                'description' => '單日連續答對10題',
                'icon' => '🔥',
                'unlocked' => true
            ];
        } elseif ($consecutiveCorrect >= 5) {
            $achievements[] = [
                'name' => '連勝專家',
                'description' => '單日連續答對5題',
                'icon' => '🔥',
                'unlocked' => true
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'achievements' => $achievements,
                'total_achievements' => count($achievements)
            ],
            'message' => '成就資料取得成功'
        ]);
    }

    /**
     * 刪除帳號
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        $member = auth('member')->user();

        // 驗證密碼
        if (!Hash::check($request->password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => '密碼錯誤'
            ], 400);
        }

        // 刪除相關資料
        \App\Models\GameRecord::where('member_id', $member->id)->delete();
        \App\Models\RoomPlayer::where('member_id', $member->id)->delete();
        
        // 刪除會員帳號
        $member->delete();

        return response()->json([
            'success' => true,
            'message' => '帳號已刪除'
        ]);
    }
} 