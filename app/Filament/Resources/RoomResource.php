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
                    ->helperText('留空將自動生成 6 位隨機代碼')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->suffixAction(
                        \Filament\Forms\Components\Actions\Action::make('generate')
                            ->label('生成')
                            ->icon('heroicon-m-sparkles')
                            ->action(function ($set) {
                                $set('code', strtoupper(\Illuminate\Support\Str::random(6)));
                            })
                    ),
                Forms\Components\Select::make('host_id')
                    ->label('房主')
                    ->relationship('host', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->email})")
                    ->searchable(['name', 'email'])
                    ->placeholder('搜尋房主姓名或電子郵件...'),
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
                Forms\Components\Section::make('遊戲設定')
                    ->schema([
                        Forms\Components\Select::make('settings.categories')
                            ->label('題目分類')
                            ->multiple()
                            ->options([
                                'daily-conversation' => '日常生活',
                                'travel-transport' => '旅遊與交通',
                                'business-english' => '商業英語',
                                'campus-life' => '校園生活',
                                'health-medical' => '健康與醫療',
                            ])
                            ->default(['daily-conversation'])
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('settings.question_count')
                            ->label('題目數量')
                            ->numeric()
                            ->default(10)
                            ->minValue(5)
                            ->maxValue(50)
                            ->required(),
                        Forms\Components\Select::make('settings.difficulty')
                            ->label('難度等級')
                            ->options([
                                'easy' => '簡單',
                                'medium' => '中等',
                                'hard' => '困難',
                                'mixed' => '混合',
                            ])
                            ->default('mixed')
                            ->required(),
                        Forms\Components\TextInput::make('settings.time_limit')
                            ->label('答題時間限制（秒）')
                            ->numeric()
                            ->default(30)
                            ->minValue(10)
                            ->maxValue(120)
                            ->required(),
                        Forms\Components\Toggle::make('settings.allow_skip')
                            ->label('允許跳過題目')
                            ->default(true),
                        Forms\Components\Toggle::make('settings.show_explanation')
                            ->label('顯示題目解釋')
                            ->default(true),
                        Forms\Components\Toggle::make('settings.auto_start')
                            ->label('人數滿時自動開始')
                            ->default(false),
                    ])
                    ->columns(2)
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
                    ->description(fn ($record) => $record->host->email ?? '')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('current_players')
                    ->label('玩家數')
                    ->formatStateUsing(fn ($record) => "{$record->current_players}/{$record->max_players}"),
                Tables\Columns\TextColumn::make('settings')
                    ->label('遊戲設定')
                    ->formatStateUsing(function ($record) {
                        $settings = $record->settings ?? [];
                        $parts = [];
                        
                        if (isset($settings['question_count'])) {
                            $parts[] = "{$settings['question_count']}題";
                        }
                        
                        if (isset($settings['time_limit'])) {
                            $parts[] = "{$settings['time_limit']}秒";
                        }
                        
                        if (isset($settings['difficulty'])) {
                            $difficultyMap = [
                                'easy' => '簡單',
                                'medium' => '中等',
                                'hard' => '困難',
                                'mixed' => '混合',
                            ];
                            $parts[] = $difficultyMap[$settings['difficulty']] ?? $settings['difficulty'];
                        }
                        
                        return implode(' | ', $parts) ?: '未設定';
                    })
                    ->limit(50),
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
                Tables\Filters\SelectFilter::make('host_id')
                    ->label('房主')
                    ->relationship('host', 'name')
                    ->searchable()
                    ->preload(),
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
