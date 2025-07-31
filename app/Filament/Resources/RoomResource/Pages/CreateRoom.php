<?php

namespace App\Filament\Resources\RoomResource\Pages;

use App\Filament\Resources\RoomResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateRoom extends CreateRecord
{
    protected static string $resource = RoomResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 如果沒有提供房間代碼，自動生成一個
        if (empty($data['code'])) {
            $data['code'] = strtoupper(Str::random(6));
        }

        // 確保設定有預設值
        $data['settings'] = array_merge([
            'categories' => ['daily-conversation'],
            'question_count' => 10,
            'difficulty' => 'mixed',
            'time_limit' => 30,
            'allow_skip' => true,
            'show_explanation' => true,
            'auto_start' => false,
        ], $data['settings'] ?? []);

        return $data;
    }
}
