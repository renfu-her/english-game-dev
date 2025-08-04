@extends('layouts.app')

@section('title', $room->name . ' - 房間')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-door-open"></i> {{ $room->name }}
                </h5>
                <div>
                    <span class="badge bg-success me-2">等待中</span>
                    <span class="badge bg-primary">{{ $room->activePlayers->count() }}/{{ $room->max_players }}</span>
                    <button class="btn btn-outline-danger btn-sm ms-2 btn-leave-room">
                        <i class="bi bi-box-arrow-left"></i> 離開房間
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>房間設定</h6>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-person"></i> 房主: {{ $room->host->name ?? '未知' }}</li>
                            <li><i class="bi bi-tag"></i> 分類: {{ $room->category->name ?? '未分類' }}</li>
                            <li><i class="bi bi-speedometer2"></i> 難度: 
                                <span class="badge difficulty-badge difficulty-{{ $room->difficulty }}">
                                    {{ ucfirst($room->difficulty) }}
                                </span>
                            </li>
                            <li><i class="bi bi-question-circle"></i> 題目數量: {{ $room->question_count }}</li>
                            <li><i class="bi bi-clock"></i> 答題時間: {{ $room->time_limit }} 秒</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>遊戲選項</h6>
                        <ul class="list-unstyled">
                            <li>
                                <i class="bi bi-{{ $room->allow_skip ? 'check-circle text-success' : 'x-circle text-danger' }}"></i>
                                允許跳過題目: {{ $room->allow_skip ? '是' : '否' }}
                            </li>
                            <li>
                                <i class="bi bi-{{ $room->show_explanation ? 'check-circle text-success' : 'x-circle text-danger' }}"></i>
                                顯示題目解釋: {{ $room->show_explanation ? '是' : '否' }}
                            </li>
                        </ul>
                    </div>
                </div>
                
                <hr>
                
                <h6>玩家列表</h6>
                <div class="row">
                    @foreach($room->activePlayers as $player)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center p-3 border rounded">
                                <div class="player-avatar me-3">
                                    {{ strtoupper(substr($player->member->name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center">
                                        <span class="ready-indicator {{ $player->is_ready ? 'ready' : 'not-ready' }}" id="ready-{{ $player->member_id }}"></span>
                                        <strong>{{ $player->member->name ?? '未知玩家' }}</strong>
                                        @if($player->member_id === $room->host_id)
                                            <span class="badge bg-warning ms-2">房主</span>
                                        @endif
                                    </div>
                                    <small class="text-muted" id="status-{{ $player->member_id }}">
                                        {{ $player->is_ready ? '已準備' : '未準備' }}
                                    </small>
                                    @if($player->member_id === Auth::guard('member')->id())
                                        <button class="btn btn-sm btn-outline-primary mt-1 toggle-ready-btn" data-room="{{ $room->id }}">
                                            {{ $player->is_ready ? '取消準備' : '準備' }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    @for($i = $room->activePlayers->count(); $i < $room->max_players; $i++)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center p-3 border rounded bg-light">
                                <div class="player-avatar me-3 bg-secondary">
                                    <i class="bi bi-person-plus text-white"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="text-muted">等待玩家加入...</div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
                
                @if($room->host_id === Auth::guard('member')->id())
                    <div class="text-center mt-4">
                        <form method="POST" action="{{ route('game.start-game', $room->id) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg" 
                                    {{ $room->activePlayers->count() < 2 ? 'disabled' : '' }}>
                                <i class="bi bi-play-circle"></i> 開始遊戲
                            </button>
                        </form>
                        @if($room->activePlayers->count() < 2)
                            <div class="text-muted mt-2">至少需要 2 名玩家才能開始遊戲</div>
                        @endif
                    </div>
                @else
                    <div class="text-center mt-4">
                        <div class="text-muted">等待房主開始遊戲...</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-chat"></i> 聊天室
                </h5>
            </div>
            <div class="card-body">
                <div id="chat-messages" style="height: 300px; overflow-y: auto;" class="mb-3">
                    <div class="text-center text-muted">
                        <i class="bi bi-chat-dots"></i>
                        <p>開始聊天吧！</p>
                    </div>
                </div>
                
                <form id="chat-form">
                    <div class="input-group">
                        <input type="text" class="form-control" id="chat-input" placeholder="輸入訊息...">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle"></i> 房間資訊
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>建立時間:</strong><br>
                    {{ $room->created_at->format('Y-m-d H:i:s') }}
                </p>
                                 <p class="mb-2">
                     <strong>房間代碼:</strong><br>
                     <code>{{ $room->code }}</code>
                 </p>
                 <p class="mb-0">
                     <strong>房間ID:</strong><br>
                     <code>{{ $room->id }}</code>
                 </p>
            </div>
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
                    
                    // 訂閱房間頻道
                    subscribeToChannel('room.{{ $room->id }}');
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
                    
                    // 訂閱房間頻道
                    subscribeToChannel('room.{{ $room->id }}');
                    
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
                .listen('.player.joined', (e) => {
                    console.log('收到玩家加入事件:', e);
                    handleWebSocketMessage({ event: 'player.joined', data: e });
                })
                .listen('.player.left', (e) => {
                    console.log('收到玩家離開事件:', e);
                    handleWebSocketMessage({ event: 'player.left', data: e });
                })
                .listen('.game.started', (e) => {
                    console.log('收到遊戲開始事件:', e);
                    handleWebSocketMessage({ event: 'game.started', data: e });
                })
                .listen('.chat.message', (e) => {
                    console.log('收到聊天訊息事件:', e);
                    handleWebSocketMessage({ event: 'chat.message', data: e });
                })
                .listen('.player.ready_status_changed', (e) => {
                    console.log('收到玩家準備狀態變更事件:', e);
                    handleWebSocketMessage({ event: 'player.ready_status_changed', data: e });
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
        
        if (data.event === 'player.joined') {
            addSystemMessage(data.data.message);
            updatePlayerCount(data.data.player_count, data.data.max_players);
            addPlayerToList(data.data.player);
        } else if (data.event === 'player.left') {
            addSystemMessage(data.data.message);
            updatePlayerCount(data.data.player_count, data.data.max_players);
            removePlayerFromList(data.data.player.id);
        } else if (data.event === 'game.started') {
            addSystemMessage('遊戲開始！正在跳轉到遊戲頁面...');
            setTimeout(() => {
                window.location.href = '{{ route("game.play", $room->id) }}';
            }, 2000);
        } else if (data.event === 'chat.message') {
            addChatMessage(data.data.sender.name, data.data.message, data.data.timestamp);
        } else if (data.event === 'player.ready_status_changed') {
            updatePlayerReadyStatus(data.data.player.id, data.data.is_ready);
            addSystemMessage(data.data.message);
        } else if (data.event === 'member.status_changed') {
            addSystemMessage(data.data.message);
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

    // 聊天功能
    $('#chat-form').on('submit', function(e) {
        e.preventDefault();
        const message = $('#chat-input').val().trim();
        if (message) {
            // 發送聊天訊息到伺服器
            $.ajax({
                url: '{{ route("game.chat", $room->id) }}',
                method: 'POST',
                data: {
                    message: message,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#chat-input').val('');
                },
                error: function(xhr) {
                    console.error('發送訊息失敗:', xhr.responseJSON);
                }
            });
        }
    });

    function addChatMessage(sender, message, timestamp) {
        const time = new Date(timestamp).toLocaleTimeString();
        const messageHtml = `
            <div class="mb-2">
                <small class="text-muted">${time}</small><br>
                <strong>${sender}:</strong> ${message}
            </div>
        `;
        $('#chat-messages').append(messageHtml);
        $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
    }

    function addSystemMessage(message) {
        const time = new Date().toLocaleTimeString();
        const messageHtml = `
            <div class="mb-2">
                <small class="text-muted">${time}</small><br>
                <em class="text-info">${message}</em>
            </div>
        `;
        $('#chat-messages').append(messageHtml);
        $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
    }

    function updatePlayerCount(current, max) {
        $('.badge.bg-primary').text(`${current}/${max}`);
    }

    function updatePlayerReadyStatus(playerId, isReady) {
        const readyIndicator = $(`#ready-${playerId}`);
        const statusText = $(`#status-${playerId}`);
        const toggleBtn = $(`.toggle-ready-btn[data-room="{{ $room->id }}"]`);
        
        if (readyIndicator.length) {
            readyIndicator.removeClass('ready not-ready').addClass(isReady ? 'ready' : 'not-ready');
        }
        
        if (statusText.length) {
            statusText.text(isReady ? '已準備' : '未準備');
        }
        
        if (toggleBtn.length && playerId == {{ Auth::guard('member')->id() }}) {
            toggleBtn.text(isReady ? '取消準備' : '準備');
        }
    }

    function addPlayerToList(player) {
        const playerList = $('.row:has(.player-avatar)').first();
        const maxPlayers = {{ $room->max_players }};
        const currentPlayers = $('.row:has(.player-avatar) .col-md-6').length;
        const currentUserId = {{ Auth::guard('member')->id() }};
        const isHost = {{ $room->host_id }} === player.id;
        
        if (currentPlayers < maxPlayers) {
            const hostBadge = isHost ? '<span class="badge bg-warning ms-2">房主</span>' : '';
            const isCurrentUser = currentUserId === player.id;
            const readyButton = isCurrentUser ? 
                '<button class="btn btn-sm btn-outline-primary mt-1 toggle-ready-btn" data-room="{{ $room->id }}">取消準備</button>' : '';
            
            const playerHtml = `
                <div class="col-md-6 mb-3" id="player-${player.id}">
                    <div class="d-flex align-items-center p-3 border rounded">
                        <div class="player-avatar me-3">
                            ${player.name.charAt(0).toUpperCase()}
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center">
                                <span class="ready-indicator ready" id="ready-${player.id}"></span>
                                <strong>${player.name}</strong>
                                ${hostBadge}
                            </div>
                            <small class="text-muted" id="status-${player.id}">已準備</small>
                            ${readyButton}
                        </div>
                    </div>
                </div>
            `;
            
            // 移除等待玩家的佔位符
            $('.row:has(.player-avatar) .col-md-6:has(.bg-light)').first().remove();
            
            // 添加新玩家
            playerList.append(playerHtml);
            
            // 重新綁定準備按鈕事件
            if (isCurrentUser) {
                $('.toggle-ready-btn').off('click').on('click', function(e) {
                    e.preventDefault();
                    const roomId = $(this).data('room');
                    
                    $.ajax({
                        url: `/game/toggle-ready/${roomId}`,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            // 狀態會通過 WebSocket 更新
                        },
                        error: function(xhr) {
                            console.error('切換準備狀態失敗:', xhr.responseJSON);
                        }
                    });
                });
            }
        }
    }

    function removePlayerFromList(playerId) {
        $(`#player-${playerId}`).remove();
        
        // 如果玩家數量少於最大值，添加等待玩家的佔位符
        const maxPlayers = {{ $room->max_players }};
        const currentPlayers = $('.row:has(.player-avatar) .col-md-6').length;
        
        if (currentPlayers < maxPlayers) {
            const placeholderHtml = `
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center p-3 border rounded bg-light">
                        <div class="player-avatar me-3 bg-secondary">
                            <i class="bi bi-person-plus text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted">等待玩家加入...</div>
                        </div>
                    </div>
                </div>
            `;
            
            $('.row:has(.player-avatar)').first().append(placeholderHtml);
        }
    }

    // 準備狀態切換功能
    $('.toggle-ready-btn').on('click', function(e) {
        e.preventDefault();
        const roomId = $(this).data('room');
        
        $.ajax({
            url: `/game/toggle-ready/${roomId}`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                // 狀態會通過 WebSocket 更新
            },
            error: function(xhr) {
                console.error('切換準備狀態失敗:', xhr.responseJSON);
            }
        });
    });

    // 離開房間功能
    $('.btn-leave-room').on('click', function(e) {
        if (confirm('確定要離開房間嗎？')) {
            const form = $('<form>', {
                'method': 'POST',
                'action': '{{ route("game.leave-room", $room->id) }}'
            }).append($('<input>', {
                'type': 'hidden',
                'name': '_token',
                'value': '{{ csrf_token() }}'
            }));
            $('body').append(form);
            form.submit();
        }
    });

    // 自動滾動到聊天底部
    $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
});
</script>
@endpush 