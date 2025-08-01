<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Room;
use App\Models\Member;

// 用戶私人頻道
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// 會員私人頻道
Broadcast::channel('App.Models.Member.{id}', function ($member, $id) {
    return (int) $member->id === (int) $id;
});

// 房間頻道 - 只有房間內的玩家可以訂閱
Broadcast::channel('room.{roomId}', function ($member, $roomId) {
    $room = Room::find($roomId);
    if (!$room) {
        return false;
    }
    
    // 檢查用戶是否在房間中
    return $room->players()->where('member_id', $member->id)->exists();
});

// 遊戲大廳頻道 - 所有已登入的會員都可以訂閱
Broadcast::channel('game.lobby', function ($member) {
    return $member instanceof Member;
});

// 遊戲進行中頻道 - 只有遊戲中的玩家可以訂閱
Broadcast::channel('game.{roomId}', function ($member, $roomId) {
    $room = Room::find($roomId);
    if (!$room || $room->status !== 'playing') {
        return false;
    }
    
    // 檢查用戶是否在遊戲房間中
    return $room->players()->where('member_id', $member->id)->exists();
});
