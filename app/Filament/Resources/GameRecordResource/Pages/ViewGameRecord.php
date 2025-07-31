<?php

namespace App\Filament\Resources\GameRecordResource\Pages;

use App\Filament\Resources\GameRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGameRecord extends ViewRecord
{
    protected static string $resource = GameRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
