<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理員儀表板 - 英語遊戲</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        .main-content {
            background-color: #f8f9fa;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .stat-card-2 {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        .stat-card-3 {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        .stat-card-4 {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- 側邊欄 -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-4">
                    <h4 class="text-white mb-4">
                        <i class="bi bi-gear-fill me-2"></i>
                        管理後台
                    </h4>
                    
                    <div class="mb-4">
                        <div class="d-flex align-items-center text-white">
                            <i class="bi bi-person-circle fs-4 me-2"></i>
                            <div>
                                <div class="fw-bold">{{ $admin->name }}</div>
                                <small class="opacity-75">{{ $admin->email }}</small>
                            </div>
                        </div>
                    </div>

                    <nav class="nav flex-column">
                        <a class="nav-link active" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i>
                            儀表板
                        </a>
                        <a class="nav-link" href="/admin/filament" target="_blank">
                            <i class="bi bi-grid me-2"></i>
                            Filament 管理
                        </a>
                        <a class="nav-link" href="{{ route('admin.logout') }}">
                            <i class="bi bi-box-arrow-right me-2"></i>
                            登出
                        </a>
                    </nav>
                </div>
            </div>

            <!-- 主要內容區 -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="mb-0">
                            <i class="bi bi-speedometer2 me-2"></i>
                            管理員儀表板
                        </h2>
                        <div class="text-muted">
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ now()->format('Y-m-d H:i') }}
                        </div>
                    </div>

                    <!-- 統計卡片 -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title opacity-75">總會員數</h6>
                                            <h3 class="mb-0">{{ \App\Models\Member::count() }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="bi bi-people-fill fs-1 opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card-2">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title opacity-75">總題目數</h6>
                                            <h3 class="mb-0">{{ \App\Models\Question::count() }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="bi bi-question-circle-fill fs-1 opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title opacity-75">活躍房間</h6>
                                            <h3 class="mb-0">{{ \App\Models\Room::where('status', 'playing')->count() }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="bi bi-door-open-fill fs-1 opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title opacity-75">遊戲記錄</h6>
                                            <h3 class="mb-0">{{ \App\Models\GameRecord::count() }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="bi bi-clock-history fs-1 opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 快速操作 -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="bi bi-lightning-fill me-2"></i>
                                        快速操作
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <a href="/admin/filament/admin/members" target="_blank" class="btn btn-outline-primary w-100">
                                                <i class="bi bi-people me-2"></i>
                                                管理會員
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="/admin/filament/admin/questions" target="_blank" class="btn btn-outline-success w-100">
                                                <i class="bi bi-question-circle me-2"></i>
                                                管理題目
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="/admin/filament/admin/rooms" target="_blank" class="btn btn-outline-info w-100">
                                                <i class="bi bi-door-open me-2"></i>
                                                管理房間
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="/admin/filament/admin/game-records" target="_blank" class="btn btn-outline-warning w-100">
                                                <i class="bi bi-clock-history me-2"></i>
                                                遊戲記錄
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 最近活動 -->
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="bi bi-clock me-2"></i>
                                        最近遊戲記錄
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        $recentRecords = \App\Models\GameRecord::with(['member', 'room', 'question'])
                                            ->latest()
                                            ->take(5)
                                            ->get();
                                    @endphp
                                    
                                    @if($recentRecords->count() > 0)
                                        <div class="list-group list-group-flush">
                                            @foreach($recentRecords as $record)
                                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <div class="fw-bold">{{ $record->member->name }}</div>
                                                        <small class="text-muted">{{ $record->room->name }}</small>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="badge {{ $record->is_correct ? 'bg-success' : 'bg-danger' }}">
                                                            {{ $record->is_correct ? '正確' : '錯誤' }}
                                                        </span>
                                                        <br>
                                                        <small class="text-muted">{{ $record->created_at->diffForHumans() }}</small>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted text-center">尚無遊戲記錄</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="bi bi-door-open me-2"></i>
                                        活躍房間
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        $activeRooms = \App\Models\Room::with('host')
                                            ->where('status', 'playing')
                                            ->latest()
                                            ->take(5)
                                            ->get();
                                    @endphp
                                    
                                    @if($activeRooms->count() > 0)
                                        <div class="list-group list-group-flush">
                                            @foreach($activeRooms as $room)
                                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <div class="fw-bold">{{ $room->name }}</div>
                                                        <small class="text-muted">房主: {{ $room->host->name }}</small>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="badge bg-success">{{ $room->status }}</span>
                                                        <br>
                                                        <small class="text-muted">{{ $room->created_at->diffForHumans() }}</small>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted text-center">目前沒有活躍房間</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // 自動刷新頁面數據（每30秒）
            setInterval(function() {
                location.reload();
            }, 30000);
        });
    </script>
</body>
</html> 