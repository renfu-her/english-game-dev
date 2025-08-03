<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Reverb 測試工具</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .test-card {
            transition: all 0.3s ease;
        }
        .test-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .status-success {
            color: #198754;
        }
        .status-error {
            color: #dc3545;
        }
        .status-warning {
            color: #ffc107;
        }
        .loading {
            display: none;
        }
        .result-box {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">
                    <i class="fas fa-broadcast-tower"></i>
                    Laravel Reverb 測試工具
                </h1>
            </div>
        </div>

        <!-- 快速測試按鈕 -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <button class="btn btn-primary btn-lg me-3" onclick="runFullTest()">
                    <i class="fas fa-play"></i> 執行完整測試
                </button>
                <button class="btn btn-secondary btn-lg" onclick="clearResults()">
                    <i class="fas fa-trash"></i> 清除結果
                </button>
            </div>
        </div>

        <div class="row">
            <!-- 配置測試 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card test-card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog"></i> 配置測試
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">測試 Laravel Reverb 配置是否正確設置</p>
                        <button class="btn btn-outline-primary" onclick="testConfiguration()">
                            <span class="loading">
                                <i class="fas fa-spinner fa-spin"></i>
                            </span>
                            <span class="normal">測試配置</span>
                        </button>
                        <div class="result-box" id="config-result" style="display: none;"></div>
                    </div>
                </div>
            </div>

            <!-- 服務器連接測試 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card test-card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-server"></i> 服務器連接
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">測試 Reverb 服務器是否可達</p>
                        <button class="btn btn-outline-primary" onclick="testServerConnection()">
                            <span class="loading">
                                <i class="fas fa-spinner fa-spin"></i>
                            </span>
                            <span class="normal">測試連接</span>
                        </button>
                        <div class="result-box" id="server-result" style="display: none;"></div>
                    </div>
                </div>
            </div>

            <!-- 廣播功能測試 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card test-card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-broadcast-tower"></i> 廣播功能
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">測試事件廣播功能</p>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="broadcast-message" 
                                   placeholder="輸入測試訊息" value="Web 介面測試訊息">
                        </div>
                        <button class="btn btn-outline-primary" onclick="testBroadcasting()">
                            <span class="loading">
                                <i class="fas fa-spinner fa-spin"></i>
                            </span>
                            <span class="normal">測試廣播</span>
                        </button>
                        <div class="result-box" id="broadcast-result" style="display: none;"></div>
                    </div>
                </div>
            </div>

            <!-- WebSocket 端點測試 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card test-card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-plug"></i> WebSocket 端點
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">測試 WebSocket 端點是否可達</p>
                        <button class="btn btn-outline-primary" onclick="testWebSocketEndpoints()">
                            <span class="loading">
                                <i class="fas fa-spinner fa-spin"></i>
                            </span>
                            <span class="normal">測試端點</span>
                        </button>
                        <div class="result-box" id="websocket-result" style="display: none;"></div>
                    </div>
                </div>
            </div>

            <!-- 頻道廣播測試 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card test-card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-signal"></i> 頻道廣播
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">測試特定頻道的廣播功能</p>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="channel-name" 
                                   placeholder="頻道名稱" value="test-channel">
                        </div>
                        <button class="btn btn-outline-primary" onclick="testChannelBroadcast()">
                            <span class="loading">
                                <i class="fas fa-spinner fa-spin"></i>
                            </span>
                            <span class="normal">測試頻道</span>
                        </button>
                        <div class="result-box" id="channel-result" style="display: none;"></div>
                    </div>
                </div>
            </div>

            <!-- 環境變數檢查 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card test-card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-envira"></i> 環境變數
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">檢查 Reverb 相關環境變數</p>
                        <button class="btn btn-outline-primary" onclick="getEnvironmentStatus()">
                            <span class="loading">
                                <i class="fas fa-spinner fa-spin"></i>
                            </span>
                            <span class="normal">檢查環境</span>
                        </button>
                        <div class="result-box" id="env-result" style="display: none;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 完整測試結果 -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-clipboard-list"></i> 完整測試結果
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="result-box" id="full-test-result" style="display: none;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // 通用函數
        function showLoading(button) {
            button.querySelector('.loading').style.display = 'inline-block';
            button.querySelector('.normal').style.display = 'none';
            button.disabled = true;
        }

        function hideLoading(button) {
            button.querySelector('.loading').style.display = 'none';
            button.querySelector('.normal').style.display = 'inline-block';
            button.disabled = false;
        }

        function showResult(elementId, data, isSuccess = true) {
            const element = document.getElementById(elementId);
            element.style.display = 'block';
            element.innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`;
            element.className = `result-box ${isSuccess ? 'status-success' : 'status-error'}`;
        }

        // 測試函數
        async function testConfiguration() {
            const button = event.target;
            showLoading(button);
            
            try {
                const response = await fetch('/test-reverb/configuration');
                const data = await response.json();
                showResult('config-result', data, true);
            } catch (error) {
                showResult('config-result', { error: error.message }, false);
            } finally {
                hideLoading(button);
            }
        }

        async function testServerConnection() {
            const button = event.target;
            showLoading(button);
            
            try {
                const response = await fetch('/test-reverb/server-connection');
                const data = await response.json();
                showResult('server-result', data, data.success);
            } catch (error) {
                showResult('server-result', { error: error.message }, false);
            } finally {
                hideLoading(button);
            }
        }

        async function testBroadcasting() {
            const button = event.target;
            const message = document.getElementById('broadcast-message').value;
            showLoading(button);
            
            try {
                const response = await fetch('/test-reverb/broadcasting', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ message })
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                showResult('broadcast-result', data, data.success);
            } catch (error) {
                showResult('broadcast-result', { 
                    error: error.message,
                    message: '請檢查瀏覽器開發者工具查看詳細錯誤信息'
                }, false);
            } finally {
                hideLoading(button);
            }
        }

        async function testWebSocketEndpoints() {
            const button = event.target;
            showLoading(button);
            
            try {
                const response = await fetch('/test-reverb/websocket-endpoints');
                const data = await response.json();
                showResult('websocket-result', data, true);
            } catch (error) {
                showResult('websocket-result', { error: error.message }, false);
            } finally {
                hideLoading(button);
            }
        }

        async function testChannelBroadcast() {
            const button = event.target;
            const channel = document.getElementById('channel-name').value;
            showLoading(button);
            
            try {
                const response = await fetch('/test-reverb/channel-broadcast', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ 
                        channel,
                        data: { message: '測試訊息', timestamp: new Date().toISOString() }
                    })
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                showResult('channel-result', data, data.success);
            } catch (error) {
                showResult('channel-result', { 
                    error: error.message,
                    message: '請檢查瀏覽器開發者工具查看詳細錯誤信息'
                }, false);
            } finally {
                hideLoading(button);
            }
        }

        async function getEnvironmentStatus() {
            const button = event.target;
            showLoading(button);
            
            try {
                const response = await fetch('/test-reverb/environment-status');
                const data = await response.json();
                showResult('env-result', data, true);
            } catch (error) {
                showResult('env-result', { error: error.message }, false);
            } finally {
                hideLoading(button);
            }
        }

        async function runFullTest() {
            const button = event.target;
            showLoading(button);
            
            try {
                const response = await fetch('/test-reverb/full-test');
                const data = await response.json();
                showResult('full-test-result', data, true);
            } catch (error) {
                showResult('full-test-result', { error: error.message }, false);
            } finally {
                hideLoading(button);
            }
        }

        function clearResults() {
            const resultBoxes = document.querySelectorAll('.result-box');
            resultBoxes.forEach(box => {
                box.style.display = 'none';
                box.innerHTML = '';
            });
        }
    </script>
</body>
</html> 