@extends('layouts.app')

@section('title', '首頁 - 英文遊戲')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-trophy"></i> 最新遊戲記錄
                </h5>
            </div>
            <div class="card-body">
                @if($gameRecords->count() > 0)
                    @foreach($gameRecords as $record)
                        <div class="game-record-item mb-3 pb-3 border-bottom">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="mb-1">{{ $record->room->name ?? '未知房間' }}</h6>
                                    <p class="text-muted mb-1">
                                        <i class="bi bi-people"></i> 
                                        {{ $record->room->players->count() ?? 0 }} 名玩家
                                        <span class="mx-2">•</span>
                                        <i class="bi bi-clock"></i> 
                                        {{ $record->created_at->diffForHumans() }}
                                    </p>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary me-2">
                                            {{ $record->room->category->name ?? '未知分類' }}
                                        </span>
                                        <span class="badge difficulty-badge difficulty-{{ $record->room->difficulty ?? 'medium' }}">
                                            {{ ucfirst($record->room->difficulty ?? 'medium') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    @if($record->winner)
                                        <div class="text-success">
                                            <i class="bi bi-crown"></i> 冠軍: {{ $record->winner->name }}
                                        </div>
                                    @endif
                                    <div class="text-muted small">
                                        得分: {{ $record->score ?? 0 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-emoji-smile display-1 text-muted"></i>
                        <p class="text-muted mt-3">還沒有遊戲記錄，開始第一場遊戲吧！</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle"></i> 遊戲說明
                </h5>
            </div>
            <div class="card-body">
                <h6>如何開始遊戲？</h6>
                <ol class="mb-3">
                    <li>註冊或登入帳號</li>
                    <li>進入遊戲大廳</li>
                    <li>建立房間或加入現有房間</li>
                    <li>等待其他玩家加入</li>
                    <li>開始遊戲並享受樂趣！</li>
                </ol>
                
                <h6>遊戲特色</h6>
                <ul class="mb-3">
                    <li>多種題目分類</li>
                    <li>三種難度等級</li>
                    <li>即時多人對戰</li>
                    <li>即時排行榜</li>
                    <li>遊戲記錄追蹤</li>
                </ul>
                
                @guest('member')
                    <div class="d-grid gap-2">
                        <a href="{{ route('member.login') }}" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i> 立即登入
                        </a>
                        <a href="{{ route('member.register') }}" class="btn btn-outline-primary">
                            <i class="bi bi-person-plus"></i> 註冊帳號
                        </a>
                    </div>
                @else
                    <div class="d-grid">
                        <a href="{{ route('game.lobby') }}" class="btn btn-primary">
                            <i class="bi bi-play-circle"></i> 開始遊戲
                        </a>
                    </div>
                @endguest
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-stats"></i> 遊戲統計
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="h4 text-primary">{{ \App\Models\Room::count() }}</div>
                        <div class="text-muted small">總房間數</div>
                    </div>
                    <div class="col-6">
                        <div class="h4 text-success">{{ \App\Models\Member::count() }}</div>
                        <div class="text-muted small">註冊玩家</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 