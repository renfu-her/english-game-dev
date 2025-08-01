@extends('layouts.app')

@section('title', '遊戲大廳')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-people"></i> 遊戲房間
                </h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoomModal">
                    <i class="bi bi-plus-circle"></i> 建立房間
                </button>
            </div>
            <div class="card-body">
                @if($rooms->count() > 0)
                    <div class="row">
                        @foreach($rooms as $room)
                            <div class="col-md-6 mb-3">
                                <div class="card room-card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-0">{{ $room->name }}</h6>
                                            <span class="badge bg-success">等待中</span>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="bi bi-person"></i> 房主: {{ $room->host->name ?? '未知' }}
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                <i class="bi bi-hash"></i> 房間代碼: <code>{{ $room->code }}</code>
                                            </small>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <span class="badge bg-primary me-1">{{ $room->category->name ?? '未分類' }}</span>
                                            <span class="badge difficulty-badge difficulty-{{ $room->difficulty }}">
                                                {{ ucfirst($room->difficulty) }}
                                            </span>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="row text-center">
                                                <div class="col-4">
                                                    <div class="small text-muted">玩家</div>
                                                    <div class="fw-bold">{{ $room->players->count() }}/{{ $room->max_players }}</div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="small text-muted">題目</div>
                                                    <div class="fw-bold">{{ $room->question_count }}</div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="small text-muted">時間</div>
                                                    <div class="fw-bold">{{ $room->time_limit }}秒</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="d-grid">
                                            <form method="POST" action="{{ route('game.join-room', $room->id) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-door-open"></i> 加入房間
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-door-closed display-1 text-muted"></i>
                        <h5 class="text-muted mt-3">目前沒有可用的房間</h5>
                        <p class="text-muted">建立第一個房間開始遊戲吧！</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoomModal">
                            <i class="bi bi-plus-circle"></i> 建立房間
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle"></i> 遊戲設定說明
                </h5>
            </div>
            <div class="card-body">
                <h6>難度等級</h6>
                <ul class="mb-3">
                    <li><span class="badge difficulty-badge difficulty-easy">Easy</span> - 適合初學者</li>
                    <li><span class="badge difficulty-badge difficulty-medium">Medium</span> - 適合一般玩家</li>
                    <li><span class="badge difficulty-badge difficulty-hard">Hard</span> - 適合進階玩家</li>
                </ul>
                
                <h6>遊戲選項</h6>
                <ul class="mb-3">
                    <li><strong>允許跳過題目</strong> - 玩家可以跳過困難的題目</li>
                    <li><strong>顯示題目解釋</strong> - 答題後顯示正確答案的解釋</li>
                </ul>
                
                <h6>人數限制</h6>
                <p class="text-muted">每個房間最多可容納 2-10 名玩家</p>
            </div>
        </div>
    </div>
</div>

<!-- 建立房間 Modal -->
<div class="modal fade" id="createRoomModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle"></i> 建立新房間
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('game.create-room') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">房間名稱</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="max_players" class="form-label">最大玩家數量</label>
                                <select class="form-select" id="max_players" name="max_players" required>
                                    @for($i = 2; $i <= 10; $i++)
                                        <option value="{{ $i }}">{{ $i }} 人</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">題目分類</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">選擇分類</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="question_count" class="form-label">題目數量</label>
                                <select class="form-select" id="question_count" name="question_count" required>
                                    @for($i = 5; $i <= 50; $i += 5)
                                        <option value="{{ $i }}">{{ $i }} 題</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="difficulty" class="form-label">難度等級</label>
                                <select class="form-select" id="difficulty" name="difficulty" required>
                                    <option value="easy">簡單</option>
                                    <option value="medium" selected>中等</option>
                                    <option value="hard">困難</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="time_limit" class="form-label">答題時間限制 (秒)</label>
                                <select class="form-select" id="time_limit" name="time_limit" required>
                                    <option value="30">30 秒</option>
                                    <option value="60" selected>60 秒</option>
                                    <option value="90">90 秒</option>
                                    <option value="120">120 秒</option>
                                    <option value="180">180 秒</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="allow_skip" name="allow_skip" value="1">
                                <label class="form-check-label" for="allow_skip">
                                    允許跳過題目
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="show_explanation" name="show_explanation" value="1" checked>
                                <label class="form-check-label" for="show_explanation">
                                    顯示題目解釋
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> 建立房間
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // 純 JavaScript WebSocket 實現
    let ws = null;
    let reconnectAttempts = 0;
    const maxReconnectAttempts = 5;
    
    function connectWebSocket() {
        try {
            // 使用 Cloudflare 域名和 /ws 路徑
            const wsUrl = 'wss://ai-tracks.com';
            
            console.log('嘗試連接到 WebSocket 服務器:', wsUrl);
            ws = new WebSocket(wsUrl);
            
            ws.onopen = function() {
                console.log('WebSocket 連接成功');
                reconnectAttempts = 0;
                
                // 訂閱遊戲大廳頻道
                subscribeToChannel('game.lobby');
            };
            
            ws.onmessage = function(event) {
                const data = JSON.parse(event.data);
                handleWebSocketMessage(data);
            };
            
            ws.onclose = function() {
                console.log('WebSocket 連接關閉');
                if (reconnectAttempts < maxReconnectAttempts) {
                    reconnectAttempts++;
                    setTimeout(connectWebSocket, 2000);
                }
            };
            
            ws.onerror = function(error) {
                console.error('WebSocket 錯誤:', error);
            };
            
        } catch (error) {
            console.error('WebSocket 連接失敗:', error);
        }
    }
    
    function subscribeToChannel(channel) {
        if (ws && ws.readyState === WebSocket.OPEN) {
            const message = {
                event: 'pusher:subscribe',
                data: {
                    channel: channel
                }
            };
            ws.send(JSON.stringify(message));
        }
    }
    
    function handleWebSocketMessage(data) {
        console.log('收到 WebSocket 訊息:', data);
        
        if (data.event === 'room.created') {
            addRoomToList(data.data.room);
            showNotification(data.data.message, 'success');
        } else if (data.event === 'room.deleted') {
            removeRoomFromList(data.data.room_id);
            showNotification(data.data.message, 'info');
        } else if (data.event === 'room.status_changed') {
            updateRoomStatus(data.data.room_id, data.data.new_status, data.data.new_status_text);
            showNotification(data.data.message, 'info');
        } else if (data.event === 'member.status_changed') {
            showNotification(data.data.message, 'info');
        }
    }
    
    // 初始化連接
    connectWebSocket();
    
    // 頁面卸載時關閉連接
    $(window).on('beforeunload', function() {
        if (ws) {
            ws.close();
        }
    });

    function addRoomToList(room) {
        const roomHtml = `
            <div class="col-md-6 mb-3" id="room-${room.id}">
                <div class="card room-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="card-title mb-0">${room.name}</h6>
                            <span class="badge bg-success">${room.status === 'waiting' ? '等待中' : room.status}</span>
                        </div>
                        
                        <div class="mb-2">
                            <small class="text-muted">
                                <i class="bi bi-person"></i> 房主: ${room.host.name}
                            </small>
                            <br>
                            <small class="text-muted">
                                <i class="bi bi-hash"></i> 房間代碼: <code>${room.code}</code>
                            </small>
                        </div>
                        
                        <div class="mb-2">
                            <span class="badge bg-primary me-1">${room.category.name}</span>
                            <span class="badge difficulty-badge difficulty-${room.difficulty}">
                                ${room.difficulty.charAt(0).toUpperCase() + room.difficulty.slice(1)}
                            </span>
                        </div>
                        
                        <div class="mb-3">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="small text-muted">玩家</div>
                                    <div class="fw-bold">${room.current_players}/${room.max_players}</div>
                                </div>
                                <div class="col-4">
                                    <div class="small text-muted">題目</div>
                                    <div class="fw-bold">${room.question_count}</div>
                                </div>
                                <div class="col-4">
                                    <div class="small text-muted">時間</div>
                                    <div class="fw-bold">${room.time_limit}秒</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <form method="POST" action="/game/join-room/${room.id}">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-door-open"></i> 加入房間
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // 添加到房間列表的開頭
        $('.row:has(.room-card)').prepend(roomHtml);
    }

    function removeRoomFromList(roomId) {
        $(`#room-${roomId}`).fadeOut(300, function() {
            $(this).remove();
        });
    }

    function updateRoomStatus(roomId, newStatus, newStatusText) {
        const roomCard = $(`#room-${roomId}`);
        const statusBadge = roomCard.find('.badge.bg-success');
        
        if (newStatus === 'waiting') {
            statusBadge.removeClass('bg-warning bg-danger').addClass('bg-success').text('等待中');
        } else if (newStatus === 'playing') {
            statusBadge.removeClass('bg-success bg-danger').addClass('bg-warning').text('遊戲中');
        } else if (newStatus === 'finished') {
            statusBadge.removeClass('bg-success bg-warning').addClass('bg-danger').text('已結束');
        }
    }

    function showNotification(message, type = 'info') {
        // 建立通知元素
        const notification = $(`
            <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'info' ? 'info-circle' : 'exclamation-circle'}"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        // 添加到頁面
        $('body').append(notification);
        
        // 3秒後自動移除
        setTimeout(() => {
            notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }
});
</script>
@endpush 