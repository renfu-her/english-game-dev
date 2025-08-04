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
    // Laravel Reverb WebSocket 實現
    let echo = null;
    let connectionStatus = 'disconnected';
    
    // 添加連接狀態指示器
    function updateConnectionStatus(status, message = '') {
        connectionStatus = status;
        console.log(`連接狀態: ${status} - ${message}`);
        
        // 可以在頁面上顯示連接狀態
        const statusElement = $('#connection-status');
        if (statusElement.length === 0) {
            $('body').prepend('<div id="connection-status" class="alert alert-info" style="position: fixed; top: 10px; right: 10px; z-index: 9999;"></div>');
        }
        
        const statusMap = {
            'connecting': { class: 'alert-warning', text: '連接中...' },
            'connected': { class: 'alert-success', text: '已連接' },
            'disconnected': { class: 'alert-danger', text: '未連接' },
            'error': { class: 'alert-danger', text: '連接錯誤: ' + message }
        };
        
        const statusInfo = statusMap[status] || statusMap['error'];
        $('#connection-status').removeClass().addClass(`alert ${statusInfo.class}`).text(statusInfo.text);
    }
    
    function tryFallbackWebSocket() {
        console.log('嘗試備用 WebSocket 連接...');
        updateConnectionStatus('connecting', '嘗試備用連接方法');
        
        // 這裡可以實現備用的 WebSocket 連接邏輯
        // 例如使用原生的 WebSocket 或 Socket.io
        console.log('備用連接方法尚未實現');
        updateConnectionStatus('error', '備用連接方法尚未實現');
    }
    
    function connectWebSocket() {
        try {
            updateConnectionStatus('connecting', '開始初始化 Echo...');
            console.log('開始初始化 Echo...');
            
            // 檢查 Echo 是否可用
            if (typeof Echo === 'undefined') {
                updateConnectionStatus('error', 'Echo 未定義！請檢查 Laravel Echo 是否正確載入');
                console.error('Echo 未定義！請檢查 Laravel Echo 是否正確載入');
                return;
            }
            
            console.log('Echo 可用，開始配置...');
            
            // 嘗試使用原生 WebSocket 連接
            try {
                console.log('嘗試使用原生 WebSocket...');
                
                const wsUrl = `ws://{{ config("broadcasting.connections.reverb.options.host") }}:{{ config("broadcasting.connections.reverb.options.port") }}/app/{{ config("broadcasting.connections.reverb.key") }}`;
                console.log('WebSocket URL:', wsUrl);
                
                const ws = new WebSocket(wsUrl);
                
                ws.onopen = function() {
                    console.log('WebSocket 連接成功');
                    updateConnectionStatus('connected', 'WebSocket 連接成功');
                    
                    // 創建一個簡單的 Echo 兼容接口
                    window.Echo = {
                        channel: function(channelName) {
                            console.log('訂閱頻道:', channelName);
                            return {
                                listen: function(event, callback) {
                                    console.log('監聽事件:', event);
                                    // 這裡可以實現事件監聽邏輯
                                    return this;
                                }
                            };
                        },
                        disconnect: function() {
                            ws.close();
                        }
                    };
                    
                    // 訂閱遊戲大廳頻道
                    subscribeToChannel('game.lobby');
                };
                
                ws.onerror = function(error) {
                    console.error('WebSocket 連接錯誤:', error);
                    updateConnectionStatus('error', 'WebSocket 連接錯誤');
                };
                
                ws.onclose = function() {
                    console.log('WebSocket 連接關閉');
                    updateConnectionStatus('disconnected', 'WebSocket 連接關閉');
                };
                
            } catch (wsError) {
                console.error('WebSocket 初始化失敗:', wsError);
                updateConnectionStatus('error', 'WebSocket 初始化失敗: ' + wsError.message);
                
                // 如果 WebSocket 失敗，嘗試 Pusher
                console.log('嘗試 Pusher 連接...');
                try {
                    const pusher = new Pusher('{{ config("broadcasting.connections.reverb.key") }}', {
                        wsHost: '{{ config("broadcasting.connections.reverb.options.host") }}',
                        wsPort: {{ config("broadcasting.connections.reverb.options.port") }},
                        forceTLS: false,
                        encrypted: false,
                        enabledTransports: ['ws', 'wss'],
                        disableStats: true,
                    });
                    
                    console.log('Pusher 實例創建成功:', pusher);
                    
                    // 創建一個簡單的 Echo 兼容接口
                    window.Echo = {
                        channel: function(channelName) {
                            const channel = pusher.subscribe(channelName);
                            return {
                                listen: function(event, callback) {
                                    channel.bind(event, callback);
                                    return this;
                                }
                            };
                        },
                        disconnect: function() {
                            pusher.disconnect();
                        }
                    };
                    
                    console.log('自定義 Echo 接口創建成功');
                    updateConnectionStatus('connected', 'Pusher 連接成功');
                    
                    // 訂閱遊戲大廳頻道
                    subscribeToChannel('game.lobby');
                    
                } catch (pusherError) {
                    console.error('Pusher 連接失敗:', pusherError);
                    updateConnectionStatus('error', 'Pusher 連接失敗: ' + pusherError.message);
                }
            }
            
        } catch (error) {
            updateConnectionStatus('error', error.message);
            console.error('Laravel Reverb 連接失敗:', error);
        }
    }
    
    function subscribeToChannel(channel) {
        console.log('嘗試訂閱頻道:', channel);
        
        // 檢查 Echo 實例和 channel 方法
        if (!window.Echo) {
            console.error('Echo 實例不存在，無法訂閱頻道');
            return;
        }
        
        if (typeof window.Echo.channel !== 'function') {
            console.error('Echo.channel 方法不存在');
            console.log('Echo 對象:', window.Echo);
            return;
        }
        
        try {
            const channelInstance = window.Echo.channel(channel);
            console.log('頻道實例創建成功:', channelInstance);
            
            channelInstance
                .listen('.room.created', (e) => {
                    console.log('收到房間創建事件:', e);
                    handleWebSocketMessage({ event: 'room.created', data: e });
                })
                .listen('.room.deleted', (e) => {
                    console.log('收到房間刪除事件:', e);
                    handleWebSocketMessage({ event: 'room.deleted', data: e });
                })
                .listen('.room.status_changed', (e) => {
                    console.log('收到房間狀態變更事件:', e);
                    handleWebSocketMessage({ event: 'room.status_changed', data: e });
                })
                .listen('.member.status_changed', (e) => {
                    console.log('收到會員狀態變更事件:', e);
                    handleWebSocketMessage({ event: 'member.status_changed', data: e });
                });
            
            console.log('頻道訂閱成功:', channel);
        } catch (error) {
            console.error('頻道訂閱失敗:', channel, error);
            console.log('錯誤詳情:', error.message);
            console.log('錯誤堆疊:', error.stack);
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
        if (window.Echo) {
            window.Echo.disconnect();
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