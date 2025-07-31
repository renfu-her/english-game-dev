<?php

namespace App\Filament\Resources\GameRecordResource\Pages;

use App\Filament\Resources\GameRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGameRecord extends EditRecord
{
    protected static string $resource = GameRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
