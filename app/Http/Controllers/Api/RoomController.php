<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomPlayer;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class RoomController extends Controller
{
    /**
     * 取得房間列表
     */
    public function index(Request $request): JsonResponse
    {
        $query = Room::with(['host', 'players']);

        // 根據狀態篩選
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // 根據分類篩選
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $rooms = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $rooms,
            'message' => '房間列表取得成功'
        ]);
    }

    /**
     * 取得單一房間詳情
     */
    public function show(Room $room): JsonResponse
    {
        $room->load(['host', 'players.member', 'category']);

        return response()->json([
            'success' => true,
            'data' => $room,
            'message' => '房間詳情取得成功'
        ]);
    }

    /**
     * 建立新房間
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'max_players' => 'required|integer|min:2|max:10',
            'question_count' => 'required|integer|min:5|max:50',
            'time_limit' => 'required|integer|min:10|max:300',
            'is_private' => 'boolean',
            'password' => 'nullable|string|min:4|max:20'
        ]);

        $member = auth('member')->user();

        $room = Room::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'host_id' => $member->id,
            'max_players' => $request->max_players,
            'question_count' => $request->question_count,
            'time_limit' => $request->time_limit,
            'is_private' => $request->is_private,
            'password' => $request->password ? bcrypt($request->password) : null,
            'status' => 'waiting',
            'code' => Str::random(6)
        ]);

        // 房主自動加入房間
        RoomPlayer::create([
            'room_id' => $room->id,
            'member_id' => $member->id,
            'is_ready' => true
        ]);

        return response()->json([
            'success' => true,
            'data' => $room->load(['host', 'players.member', 'category']),
            'message' => '房間建立成功'
        ], 201);
    }

    /**
     * 更新房間
     */
    public function update(Request $request, Room $room): JsonResponse
    {
        $member = auth('member')->user();

        // 只有房主可以更新房間
        if ($room->host_id !== $member->id) {
            return response()->json([
                'success' => false,
                'message' => '只有房主可以更新房間設定'
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'max_players' => 'required|integer|min:2|max:10',
            'question_count' => 'required|integer|min:5|max:50',
            'time_limit' => 'required|integer|min:10|max:300',
            'is_private' => 'boolean',
            'password' => 'nullable|string|min:4|max:20'
        ]);

        $room->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'max_players' => $request->max_players,
            'question_count' => $request->question_count,
            'time_limit' => $request->time_limit,
            'is_private' => $request->is_private,
            'password' => $request->password ? bcrypt($request->password) : null,
        ]);

        return response()->json([
            'success' => true,
            'data' => $room->load(['host', 'players.member', 'category']),
            'message' => '房間更新成功'
        ]);
    }

    /**
     * 刪除房間
     */
    public function destroy(Room $room): JsonResponse
    {
        $member = auth('member')->user();

        // 只有房主可以刪除房間
        if ($room->host_id !== $member->id) {
            return response()->json([
                'success' => false,
                'message' => '只有房主可以刪除房間'
            ], 403);
        }

        $room->delete();

        return response()->json([
            'success' => true,
            'message' => '房間刪除成功'
        ]);
    }

    /**
     * 加入房間
     */
    public function join(Request $request, Room $room): JsonResponse
    {
        $request->validate([
            'password' => 'nullable|string'
        ]);

        $member = auth('member')->user();

        // 檢查房間是否已滿
        if ($room->players()->count() >= $room->max_players) {
            return response()->json([
                'success' => false,
                'message' => '房間已滿'
            ], 400);
        }

        // 檢查是否已在房間中
        if ($room->players()->where('member_id', $member->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => '您已在房間中'
            ], 400);
        }

        // 檢查房間狀態
        if ($room->status !== 'waiting') {
            return response()->json([
                'success' => false,
                'message' => '房間已開始遊戲'
            ], 400);
        }

        // 檢查密碼
        if ($room->is_private && !Hash::check($request->password, $room->password)) {
            return response()->json([
                'success' => false,
                'message' => '房間密碼錯誤'
            ], 400);
        }

        RoomPlayer::create([
            'room_id' => $room->id,
            'member_id' => $member->id,
            'is_ready' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => '成功加入房間'
        ]);
    }

    /**
     * 離開房間
     */
    public function leave(Room $room): JsonResponse
    {
        $member = auth('member')->user();

        $player = $room->players()->where('member_id', $member->id)->first();

        if (!$player) {
            return response()->json([
                'success' => false,
                'message' => '您不在房間中'
            ], 400);
        }

        $player->delete();

        // 如果房主離開，轉移房主權限或關閉房間
        if ($room->host_id === $member->id) {
            $nextPlayer = $room->players()->first();
            if ($nextPlayer) {
                $room->update(['host_id' => $nextPlayer->member_id]);
            } else {
                $room->delete();
                return response()->json([
                    'success' => true,
                    'message' => '房主離開，房間已關閉'
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => '成功離開房間'
        ]);
    }

    /**
     * 準備/取消準備
     */
    public function toggleReady(Room $room): JsonResponse
    {
        $member = auth('member')->user();

        $player = $room->players()->where('member_id', $member->id)->first();

        if (!$player) {
            return response()->json([
                'success' => false,
                'message' => '您不在房間中'
            ], 400);
        }

        $player->update(['is_ready' => !$player->is_ready]);

        return response()->json([
            'success' => true,
            'data' => ['is_ready' => $player->is_ready],
            'message' => $player->is_ready ? '已準備' : '已取消準備'
        ]);
    }

    /**
     * 開始遊戲
     */
    public function startGame(Room $room): JsonResponse
    {
        $member = auth('member')->user();

        // 只有房主可以開始遊戲
        if ($room->host_id !== $member->id) {
            return response()->json([
                'success' => false,
                'message' => '只有房主可以開始遊戲'
            ], 403);
        }

        // 檢查房間狀態
        if ($room->status !== 'waiting') {
            return response()->json([
                'success' => false,
                'message' => '遊戲已開始'
            ], 400);
        }

        // 檢查玩家數量
        if ($room->players()->count() < 2) {
            return response()->json([
                'success' => false,
                'message' => '至少需要2名玩家才能開始遊戲'
            ], 400);
        }

        // 檢查所有玩家是否準備
        $unreadyPlayers = $room->players()->where('is_ready', false)->count();
        if ($unreadyPlayers > 0) {
            return response()->json([
                'success' => false,
                'message' => '還有玩家未準備'
            ], 400);
        }

        $room->update(['status' => 'playing']);

        return response()->json([
            'success' => true,
            'message' => '遊戲開始'
        ]);
    }
} 