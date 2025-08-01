<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>會員儀表板 - 英語遊戲</title>
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
        .avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
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
                        <i class="bi bi-person-fill me-2"></i>
                        會員中心
                    </h4>
                    
                    <div class="mb-4">
                        <div class="d-flex align-items-center text-white">
                            @if($member->avatar)
                                <img src="{{ asset('storage/' . $member->avatar) }}" alt="Avatar" class="avatar me-3">
                            @else
                                <i class="bi bi-person-circle fs-1 me-3"></i>
                            @endif
                            <div>
                                <div class="fw-bold">{{ $member->name }}</div>
                                <small class="opacity-75">{{ $member->email }}</small>
                            </div>
                        </div>
                    </div>

                    <nav class="nav flex-column">
                        <a class="nav-link active" href="{{ route('member.dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i>
                            儀表板
                        </a>
                        <a class="nav-link" href="#">
                            <i class="bi bi-trophy me-2"></i>
                            遊戲記錄
                        </a>
                        <a class="nav-link" href="#">
                            <i class="bi bi-gear me-2"></i>
                            個人設定
                        </a>
                        <a class="nav-link" href="{{ route('member.logout') }}">
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
                            歡迎回來，{{ $member->name }}！
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
                                            <h6 class="card-title opacity-75">總遊戲數</h6>
                                            <h3 class="mb-0">{{ $member->total_games }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="bi bi-controller fs-1 opacity-75"></i>
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
                                            <h6 class="card-title opacity-75">正確答案</h6>
                                            <h3 class="mb-0">{{ $member->correct_answers }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="bi bi-check-circle-fill fs-1 opacity-75"></i>
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
                                            <h6 class="card-title opacity-75">總答題數</h6>
                                            <h3 class="mb-0">{{ $member->total_answers }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="bi bi-question-circle-fill fs-1 opacity-75"></i>
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
                                            <h6 class="card-title opacity-75">正確率</h6>
                                            <h3 class="mb-0">{{ $member->accuracy }}%</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="bi bi-percent fs-1 opacity-75"></i>
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
                                        快速開始
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <a href="#" class="btn btn-outline-primary w-100">
                                                <i class="bi bi-plus-circle me-2"></i>
                                                創建房間
                                            </a>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <a href="#" class="btn btn-outline-success w-100">
                                                <i class="bi bi-door-open me-2"></i>
                                                加入房間
                                            </a>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <a href="#" class="btn btn-outline-info w-100">
                                                <i class="bi bi-list-ul me-2"></i>
                                                房間列表
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
                                        $recentRecords = \App\Models\GameRecord::with(['room', 'question'])
                                            ->where('member_id', $member->id)
                                            ->latest()
                                            ->take(5)
                                            ->get();
                                    @endphp
                                    
                                    @if($recentRecords->count() > 0)
                                        <div class="list-group list-group-flush">
                                            @foreach($recentRecords as $record)
                                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <div class="fw-bold">{{ $record->room->name }}</div>
                                                        <small class="text-muted">{{ Str::limit($record->question->question, 30) }}</small>
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
                                        <i class="bi bi-trophy me-2"></i>
                                        成就統計
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6 mb-3">
                                            <div class="border-end">
                                                <h4 class="text-primary">{{ $member->total_games }}</h4>
                                                <small class="text-muted">參與遊戲</small>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <h4 class="text-success">{{ $member->correct_answers }}</h4>
                                            <small class="text-muted">正確答案</small>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-info">{{ $member->total_answers }}</h4>
                                            <small class="text-muted">總答題數</small>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-warning">{{ $member->accuracy }}%</h4>
                                            <small class="text-muted">正確率</small>
                                        </div>
                                    </div>
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
            // 自動刷新頁面數據（每60秒）
            setInterval(function() {
                location.reload();
            }, 60000);
        });
    </script>
</body>
</html> 