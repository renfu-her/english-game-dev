<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Category;
use App\Models\GameRecord;
use App\Events\PlayerJoinedRoom;
use App\Events\PlayerLeftRoom;
use App\Events\GameStarted;
use App\Events\ChatMessage;
use App\Events\PlayerReadyStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    public function index()
    {
        $gameRecords = GameRecord::with(['room', 'winner'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        return view('game.index', compact('gameRecords'));
    }

    public function lobby()
    {
        $rooms = Room::with(['host', 'players', 'category'])
            ->where('status', 'waiting')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $categories = Category::all();
        
        return view('game.lobby', compact('rooms', 'categories'));
    }

    public function createRoom(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'max_players' => 'required|integer|min:2|max:10',
            'category_id' => 'required|exists:categories,id',
            'question_count' => 'required|integer|min:5|max:50',
            'difficulty' => 'required|in:easy,medium,hard',
            'time_limit' => 'required|integer|min:10|max:300',
            'allow_skip' => 'boolean',
            'show_explanation' => 'boolean',
        ]);

        $room = Room::create([
            'name' => $request->name,
            'host_id' => Auth::guard('member')->id(),
            'max_players' => $request->max_players,
            'category_id' => $request->category_id,
            'question_count' => $request->question_count,
            'difficulty' => $request->difficulty,
            'time_limit' => $request->time_limit,
            'allow_skip' => $request->boolean('allow_skip'),
            'show_explanation' => $request->boolean('show_explanation'),
            'status' => 'waiting',
        ]);

        // 房主自動加入房間
        $room->players()->create([
            'member_id' => Auth::guard('member')->id(),
            'is_ready' => true,
        ]);

        // 廣播玩家加入房間事件
        $member = Auth::guard('member')->user();
        broadcast(new PlayerJoinedRoom($room, $member))->toOthers();

        return redirect()->route('game.room', $room->id);
    }

    public function joinRoom(Room $room)
    {
        if ($room->status !== 'waiting') {
            return redirect()->route('game.lobby')->with('error', '房間已開始遊戲或已結束');
        }

        if ($room->players()->count() >= $room->max_players) {
            return redirect()->route('game.lobby')->with('error', '房間已滿');
        }

        // 檢查是否已經在房間中
        $existingPlayer = $room->players()->where('member_id', Auth::guard('member')->id())->first();
        if ($existingPlayer) {
            return redirect()->route('game.room', $room->id);
        }

        $room->players()->create([
            'member_id' => Auth::guard('member')->id(),
            'is_ready' => true, // 新加入的玩家預設為準備狀態
        ]);

        // 廣播玩家加入房間事件
        $member = Auth::guard('member')->user();
        broadcast(new PlayerJoinedRoom($room, $member))->toOthers();

        // 廣播玩家準備狀態事件
        broadcast(new PlayerReadyStatusChanged($room, $member, true));

        return redirect()->route('game.room', $room->id);
    }

    public function room(Room $room)
    {
        $room->load(['host', 'players.member', 'category']);
        
        return view('game.room', compact('room'));
    }

    public function startGame(Room $room)
    {
        if ($room->host_id !== Auth::guard('member')->id()) {
            return redirect()->route('game.room', $room->id)->with('error', '只有房主可以開始遊戲');
        }

        if ($room->players()->count() < 2) {
            return redirect()->route('game.room', $room->id)->with('error', '至少需要2名玩家才能開始遊戲');
        }

        $room->update(['status' => 'playing']);
        
        // 廣播遊戲開始事件
        broadcast(new GameStarted($room));
        
        return redirect()->route('game.play', $room->id);
    }

    public function play(Room $room)
    {
        if ($room->status !== 'playing') {
            return redirect()->route('game.room', $room->id);
        }

        $room->load(['host', 'players.member', 'category']);
        
        return view('game.play', compact('room'));
    }

    public function sendChatMessage(Request $request, Room $room)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $member = Auth::guard('member')->user();
        
        // 檢查用戶是否在房間中
        if (!$room->players()->where('member_id', $member->id)->exists()) {
            return response()->json(['error' => '您不在這個房間中'], 403);
        }

        // 廣播聊天訊息
        broadcast(new ChatMessage($room, $member, $request->message));

        return response()->json(['success' => true]);
    }

    public function leaveRoom(Room $room)
    {
        $member = Auth::guard('member')->user();
        
        // 檢查用戶是否在房間中
        $player = $room->players()->where('member_id', $member->id)->first();
        if (!$player) {
            return redirect()->route('game.lobby')->with('error', '您不在這個房間中');
        }

        // 刪除玩家記錄
        $player->delete();

        // 廣播玩家離開房間事件
        broadcast(new PlayerLeftRoom($room, $member))->toOthers();

        // 如果房間空了，刪除房間
        if ($room->players()->count() === 0) {
            $room->delete();
            return redirect()->route('game.lobby')->with('success', '房間已關閉');
        }

        // 如果房主離開，轉移房主權限給下一個玩家
        if ($room->host_id === $member->id) {
            $newHost = $room->players()->first();
            if ($newHost) {
                $room->update(['host_id' => $newHost->member_id]);
            }
        }

        return redirect()->route('game.lobby')->with('success', '已離開房間');
    }

    public function toggleReadyStatus(Room $room)
    {
        $member = Auth::guard('member')->user();
        
        // 檢查用戶是否在房間中
        $player = $room->players()->where('member_id', $member->id)->first();
        if (!$player) {
            return response()->json(['error' => '您不在這個房間中'], 403);
        }

        // 切換準備狀態
        $newReadyStatus = !$player->is_ready;
        $player->update(['is_ready' => $newReadyStatus]);

        // 廣播準備狀態變更事件
        broadcast(new PlayerReadyStatusChanged($room, $member, $newReadyStatus));

        return response()->json([
            'success' => true,
            'is_ready' => $newReadyStatus,
            'message' => $newReadyStatus ? '已準備' : '取消準備'
        ]);
    }

    public function setAllPlayersReady(Room $room)
    {
        if ($room->host_id !== Auth::guard('member')->id()) {
            return response()->json(['error' => '只有房主可以設定所有玩家準備'], 403);
        }

        // 將所有玩家設為準備狀態
        $room->players()->update(['is_ready' => true]);

        // 廣播每個玩家的準備狀態變更
        foreach ($room->players as $player) {
            broadcast(new PlayerReadyStatusChanged($room, $player->member, true));
        }

        return response()->json(['success' => true, 'message' => '所有玩家已設為準備狀態']);
    }
} 