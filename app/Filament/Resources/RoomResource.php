<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\Pages;
use App\Filament\Resources\RoomResource\RelationManagers;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $navigationGroup = '遊戲管理';
    protected static ?string $navigationLabel = '房間管理';
    protected static ?string $modelLabel = '房間';
    protected static ?string $pluralModelLabel = '房間';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('房間名稱')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->label('房間代碼')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('host_id')
                    ->label('房主')
                    ->relationship('host', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('max_players')
                    ->label('最大玩家數')
                    ->numeric()
                    ->required()
                    ->minValue(2)
                    ->maxValue(10),
                Forms\Components\TextInput::make('current_players')
                    ->label('當前玩家數')
                    ->numeric()
                    ->default(0),
                Forms\Components\Select::make('status')
                    ->label('狀態')
                    ->options([
                        'waiting' => '等待中',
                        'playing' => '遊戲中',
                        'finished' => '已結束',
                    ])
                    ->default('waiting')
                    ->required(),
                Forms\Components\KeyValue::make('settings')
                    ->label('設定')
                    ->keyLabel('設定項目')
                    ->valueLabel('設定值')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('房間名稱')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('房間代碼')
                    ->searchable(),
                Tables\Columns\TextColumn::make('host.name')
                    ->label('房主')
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_players')
                    ->label('玩家數')
                    ->formatStateUsing(fn ($record) => "{$record->current_players}/{$record->max_players}"),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('狀態')
                    ->colors([
                        'warning' => 'waiting',
                        'primary' => 'playing',
                        'success' => 'finished',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'waiting' => '等待中',
                        'playing' => '遊戲中',
                        'finished' => '已結束',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('建立時間')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('狀態')
                    ->options([
                        'waiting' => '等待中',
                        'playing' => '遊戲中',
                        'finished' => '已結束',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }
}
