@extends('layouts.app')

@section('title', $room->name . ' - 遊戲進行中')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-controller"></i> {{ $room->name }} - 遊戲進行中
                </h5>
                <div>
                    <span class="badge bg-warning me-2">進行中</span>
                    <span class="badge bg-primary">{{ $room->players->count() }}/{{ $room->max_players }}</span>
                </div>
            </div>
            <div class="card-body">
                <!-- 遊戲進度 -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="progress mb-2">
                            <div class="progress-bar" id="question-progress" role="progressbar" style="width: 0%"></div>
                        </div>
                        <small class="text-muted">題目進度: <span id="current-question">0</span> / <span id="total-questions">{{ $room->question_count }}</span></small>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="timer-display">
                            <i class="bi bi-clock"></i>
                            <span id="timer" class="h4 text-primary">{{ $room->time_limit }}</span>
                            <small class="text-muted">秒</small>
                        </div>
                    </div>
                </div>

                <!-- 題目顯示區域 -->
                <div id="question-container" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">載入中...</span>
                    </div>
                    <p class="mt-3">正在載入題目...</p>
                </div>

                <!-- 答案選項 -->
                <div id="answer-container" class="mt-4" style="display: none;">
                    <div class="row" id="answer-options">
                        <!-- 答案選項將在這裡動態生成 -->
                    </div>
                </div>

                <!-- 遊戲結果 -->
                <div id="result-container" class="mt-4" style="display: none;">
                    <div class="alert" id="result-alert">
                        <!-- 結果將在這裡顯示 -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-chat"></i> 遊戲聊天
                </h5>
            </div>
            <div class="card-body">
                <div id="chat-messages" style="height: 300px; overflow-y: auto;" class="mb-3">
                    <div class="text-center text-muted">
                        <i class="bi bi-chat-dots"></i>
                        <p>遊戲聊天室</p>
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
                    <i class="bi bi-people"></i> 玩家列表
                </h5>
            </div>
            <div class="card-body">
                <div id="players-list">
                    @foreach($room->players as $player)
                        <div class="d-flex align-items-center mb-2">
                            <div class="player-avatar me-2">
                                {{ strtoupper(substr($player->member->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <strong>{{ $player->member->name ?? '未知玩家' }}</strong>
                                @if($player->member_id === $room->host_id)
                                    <span class="badge bg-warning ms-1">房主</span>
                                @endif
                            </div>
                            <div class="score-display">
                                <span class="badge bg-success" id="score-{{ $player->member_id }}">0</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentQuestion = 0;
    let totalQuestions = {{ $room->question_count }};
    let timer = null;
    let timeLeft = {{ $room->time_limit }};
    let gameStarted = false;
    
    // Laravel Reverb WebSocket 實現
    let echo = null;
    
    function connectWebSocket() {
        try {
            // 使用 Laravel Reverb
            window.Echo = new Echo({
                broadcaster: 'reverb',
                key: '{{ config("broadcasting.connections.reverb.key") }}',
                wsHost: '{{ config("broadcasting.connections.reverb.options.host") }}',
                wsPort: {{ config("broadcasting.connections.reverb.options.port") }},
                wssPort: {{ config("broadcasting.connections.reverb.options.port") }},
                forceTLS: false,
                enabledTransports: ['ws', 'wss'],
                disableStats: true,
                // 添加認證配置
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }
            });
            
            echo = window.Echo;
            console.log('Laravel Reverb 連接成功');
            
            // 訂閱遊戲頻道
            subscribeToChannel('game.{{ $room->id }}');
            
        } catch (error) {
            console.error('Laravel Reverb 連接失敗:', error);
        }
    }
    
    function subscribeToChannel(channel) {
        if (echo) {
            echo.channel(channel)
                .listen('.question.displayed', (e) => {
                    handleWebSocketMessage({ event: 'question.displayed', data: e });
                })
                .listen('.chat.message', (e) => {
                    handleWebSocketMessage({ event: 'chat.message', data: e });
                })
                .listen('.game.ended', (e) => {
                    handleWebSocketMessage({ event: 'game.ended', data: e });
                });
        }
    }
    
    function handleWebSocketMessage(data) {
        console.log('收到 WebSocket 訊息:', data);
        
        if (data.event === 'question.displayed') {
            displayQuestion(data.data.question, data.data.question_number, data.data.total_questions);
            startTimer(data.data.time_limit);
        } else if (data.event === 'chat.message') {
            addChatMessage(data.data.sender.name, data.data.message, data.data.timestamp);
        }
    }
    
    // 初始化連接
    connectWebSocket();
    
    // 頁面卸載時關閉連接
    $(window).on('beforeunload', function() {
        if (echo) {
            echo.disconnect();
        }
    });

    // 聊天功能
    $('#chat-form').on('submit', function(e) {
        e.preventDefault();
        const message = $('#chat-input').val().trim();
        if (message) {
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

    function displayQuestion(question, questionNumber, totalQuestions) {
        currentQuestion = questionNumber;
        totalQuestions = totalQuestions;
        
        // 更新進度條
        const progress = (questionNumber / totalQuestions) * 100;
        $('#question-progress').css('width', progress + '%');
        $('#current-question').text(questionNumber);
        $('#total-questions').text(totalQuestions);

        // 顯示題目
        $('#question-container').html(`
            <h4 class="mb-4">${question.question}</h4>
        `);

        // 生成答案選項
        let optionsHtml = '';
        if (question.type === 'multiple_choice') {
            question.options.forEach((option, index) => {
                optionsHtml += `
                    <div class="col-md-6 mb-3">
                        <button class="btn btn-outline-primary w-100 answer-btn" data-answer="${option}">
                            ${String.fromCharCode(65 + index)}. ${option}
                        </button>
                    </div>
                `;
            });
        } else if (question.type === 'fill_blank') {
            question.options.forEach((option, index) => {
                optionsHtml += `
                    <div class="col-md-6 mb-3">
                        <button class="btn btn-outline-primary w-100 answer-btn" data-answer="${option}">
                            ${index + 1}. ${option}
                        </button>
                    </div>
                `;
            });
        }

        $('#answer-options').html(optionsHtml);
        $('#answer-container').show();

        // 綁定答案按鈕事件
        $('.answer-btn').on('click', function() {
            const selectedAnswer = $(this).data('answer');
            submitAnswer(question.id, selectedAnswer);
        });
    }

    function startTimer(duration) {
        timeLeft = duration;
        $('#timer').text(timeLeft);
        
        if (timer) clearInterval(timer);
        
        timer = setInterval(function() {
            timeLeft--;
            $('#timer').text(timeLeft);
            
            if (timeLeft <= 0) {
                clearInterval(timer);
                // 時間到，自動提交答案或跳過
                if ($('#answer-container').is(':visible')) {
                    submitAnswer(null, null); // 時間到，沒有答案
                }
            }
        }, 1000);
    }

    function submitAnswer(questionId, answer) {
        // 禁用所有答案按鈕
        $('.answer-btn').prop('disabled', true);
        
        // 顯示結果
        $('#result-container').show();
        $('#result-alert').removeClass('alert-success alert-danger').addClass('alert-info')
            .html('<i class="bi bi-hourglass-split"></i> 正在處理答案...');

        // 這裡可以發送答案到伺服器進行驗證
        // 暫時模擬結果
        setTimeout(() => {
            const isCorrect = Math.random() > 0.5; // 模擬隨機結果
            showResult(isCorrect, answer);
        }, 1000);
    }

    function showResult(isCorrect, answer) {
        const resultClass = isCorrect ? 'alert-success' : 'alert-danger';
        const resultIcon = isCorrect ? 'bi-check-circle' : 'bi-x-circle';
        const resultText = isCorrect ? '答對了！' : '答錯了！';
        
        $('#result-alert').removeClass('alert-info').addClass(resultClass)
            .html(`<i class="bi ${resultIcon}"></i> ${resultText} 您的答案: ${answer || '未作答'}`);

        // 3秒後進入下一題
        setTimeout(() => {
            if (currentQuestion < totalQuestions) {
                // 載入下一題
                $('#result-container').hide();
                $('#answer-container').hide();
                $('#question-container').html(`
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">載入中...</span>
                    </div>
                    <p class="mt-3">正在載入下一題...</p>
                `);
            } else {
                // 遊戲結束
                showGameEnd();
            }
        }, 3000);
    }

    function showGameEnd() {
        $('#question-container').html(`
            <div class="text-center">
                <i class="bi bi-trophy display-1 text-warning"></i>
                <h3 class="mt-3">遊戲結束！</h3>
                <p class="text-muted">感謝您的參與</p>
                <a href="{{ route('game.lobby') }}" class="btn btn-primary">
                    <i class="bi bi-house"></i> 返回大廳
                </a>
            </div>
        `);
        $('#answer-container').hide();
        $('#result-container').hide();
    }

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

    // 自動滾動到聊天底部
    $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
});
</script>
@endpush 