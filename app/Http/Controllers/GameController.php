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
use App\Events\RoomCreated;
use App\Events\RoomDeleted;
use App\Events\RoomStatusChanged;
use App\Events\MemberStatusChanged;
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
        $rooms = Room::with(['host', 'activePlayers.member', 'category'])
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

        // 生成唯一的房間代碼
        do {
            $code = strtoupper(substr(md5(uniqid()), 0, 6));
        } while (Room::where('code', $code)->exists());

        $room = Room::create([
            'name' => $request->name,
            'code' => $code,
            'host_id' => Auth::guard('member')->id(),
            'max_players' => $request->max_players,
            'category_id' => $request->category_id,
            'question_count' => $request->question_count,
            'difficulty' => $request->difficulty,
            'time_limit' => $request->time_limit,
            'allow_skip' => $request->boolean('allow_skip'),
            'show_explanation' => $request->boolean('show_explanation'),
            'status' => 'waiting',
            'settings' => [
                'categories' => [$request->category_id],
                'question_count' => $request->question_count,
                'difficulty' => $request->difficulty,
                'time_limit' => $request->time_limit,
                'allow_skip' => $request->boolean('allow_skip'),
                'show_explanation' => $request->boolean('show_explanation'),
                'auto_start' => false,
            ],
        ]);

        // 房主自動加入房間
        $room->players()->create([
            'member_id' => Auth::guard('member')->id(),
            'is_ready' => true,
            'joined_at' => now(),
        ]);

        // 廣播房間建立事件
        broadcast(new RoomCreated($room));
        
        // 廣播會員狀態變更事件
        $member = Auth::guard('member')->user();
        broadcast(new MemberStatusChanged($member, 'in_room', $room->id));

        return redirect()->route('game.room', $room->id);
    }

    public function joinRoom(Room $room)
    {
        $member = Auth::guard('member')->user();
        
        // 檢查房間是否已滿
        $activePlayerCount = $room->activePlayers()->count();
        if ($activePlayerCount >= $room->max_players) {
            return redirect()->route('game.lobby')->with('error', '房間已滿');
        }
        
        // 檢查用戶是否已經在房間中
        $existingPlayer = $room->players()->where('member_id', $member->id)->first();
        
        if ($existingPlayer) {
            // 如果玩家之前離開過，重新加入
            if ($existingPlayer->left_at) {
                $existingPlayer->update([
                    'left_at' => null,
                    'is_ready' => false,
                    'joined_at' => now()
                ]);
                
                // 廣播玩家重新加入事件
                broadcast(new PlayerJoinedRoom($room, $member));
                broadcast(new MemberStatusChanged($member, 'in_room', $room->id));
                
                return redirect()->route('game.room', $room->id)->with('success', '重新加入房間成功');
            } else {
                return redirect()->route('game.room', $room->id)->with('info', '您已經在房間中');
            }
        }
        
        // 創建新的玩家記錄
        $room->players()->create([
            'member_id' => $member->id,
            'is_ready' => false,
            'joined_at' => now(),
        ]);
        
        // 廣播玩家加入事件
        broadcast(new PlayerJoinedRoom($room, $member));
        broadcast(new MemberStatusChanged($member, 'in_room', $room->id));
        
        return redirect()->route('game.room', $room->id)->with('success', '成功加入房間');
    }

    public function room(Room $room)
    {
        if ($room->status === 'playing') {
            return redirect()->route('game.play', $room->id);
        }

        $room->load(['host', 'activePlayers.member', 'category']);
        
        return view('game.room', compact('room'));
    }

    public function startGame(Room $room)
    {
        if ($room->host_id !== Auth::guard('member')->id()) {
            return response()->json(['error' => '只有房主可以開始遊戲'], 403);
        }

        // 檢查所有玩家是否都準備好了
        $unreadyPlayers = $room->activePlayers()->where('is_ready', false)->count();
        if ($unreadyPlayers > 0) {
            return response()->json(['error' => '還有玩家未準備'], 400);
        }

        // 更新房間狀態
        $room->update(['status' => 'playing']);

        // 廣播遊戲開始事件
        broadcast(new GameStarted($room));
        
        // 廣播所有玩家的狀態變更為遊戲中
        foreach ($room->activePlayers as $player) {
            broadcast(new MemberStatusChanged($player->member, 'playing', $room->id));
        }

        return response()->json(['success' => true, 'message' => '遊戲開始！']);
    }

    public function play(Room $room)
    {
        if ($room->status !== 'playing') {
            return redirect()->route('game.room', $room->id);
        }

        $room->load(['host', 'activePlayers.member', 'category']);
        
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

        // 更新離開時間而不是刪除記錄
        $player->update([
            'left_at' => now(),
            'is_ready' => false
        ]);

        // 重新載入房間以獲取最新的玩家數量
        $room->refresh();

        // 廣播玩家離開房間事件
        broadcast(new PlayerLeftRoom($room, $member));
        
        // 廣播會員狀態變更事件 - 使用更明確的狀態
        broadcast(new MemberStatusChanged($member, 'left_room', $room->id));

        // 如果房間沒有活躍玩家，刪除房間
        $activePlayers = $room->players()->whereNull('left_at')->count();
        if ($activePlayers === 0) {
            $roomName = $room->name;
            $roomId = $room->id;
            $room->delete();
            
            // 廣播房間刪除事件
            broadcast(new RoomDeleted($roomId, $roomName));
            
            return redirect()->route('game.lobby')->with('success', '房間已關閉');
        }

        // 如果房主離開，轉移房主權限給下一個活躍玩家
        if ($room->host_id === $member->id) {
            $newHost = $room->players()->whereNull('left_at')->first();
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
        $player = $room->activePlayers()->where('member_id', $member->id)->first();
        if (!$player) {
            return response()->json(['error' => '您不在這個房間中'], 403);
        }

        // 切換準備狀態
        $newReadyStatus = !$player->is_ready;
        $player->update(['is_ready' => $newReadyStatus]);

        // 廣播準備狀態變更事件
        broadcast(new PlayerReadyStatusChanged($room, $member, $newReadyStatus));
        
        // 廣播會員狀態變更事件
        $status = $newReadyStatus ? 'ready' : 'not_ready';
        broadcast(new MemberStatusChanged($member, $status, $room->id));

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

        // 將所有活躍玩家設為準備狀態
        $room->activePlayers()->update(['is_ready' => true]);

        // 廣播每個玩家的準備狀態變更
        foreach ($room->activePlayers as $player) {
            broadcast(new PlayerReadyStatusChanged($room, $player->member, true));
        }

        return response()->json(['success' => true, 'message' => '所有玩家已設為準備狀態']);
    }
} 