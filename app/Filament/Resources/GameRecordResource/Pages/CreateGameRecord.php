<?php

namespace App\Filament\Resources\GameRecordResource\Pages;

use App\Filament\Resources\GameRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGameRecord extends CreateRecord
{
    protected static string $resource = GameRecordResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
