<?php

namespace App\Filament\Resources\GameRecordResource\Pages;

use App\Filament\Resources\GameRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGameRecords extends ListRecords
{
    protected static string $resource = GameRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
