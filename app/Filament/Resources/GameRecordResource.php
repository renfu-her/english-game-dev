<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameRecordResource\Pages;
use App\Filament\Resources\GameRecordResource\RelationManagers;
use App\Models\GameRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GameRecordResource extends Resource
{
    protected static ?string $model = GameRecord::class;

    protected static ?string $navigationGroup = '遊戲管理';
    protected static ?string $navigationLabel = '遊戲記錄';
    protected static ?string $modelLabel = '遊戲記錄';
    protected static ?string $pluralModelLabel = '遊戲記錄';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('room_id')
                    ->label('房間')
                    ->relationship('room', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('user_id')
                    ->label('玩家')
                    ->relationship('member', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->email})")
                    ->searchable(['name', 'email']),
                Forms\Components\Select::make('question_id')
                    ->label('題目')
                    ->relationship('question', 'question')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->question} ({$record->category->name})")
                    ->searchable(['question']),
                Forms\Components\TextInput::make('user_answer')
                    ->label('玩家答案')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_correct')
                    ->label('是否正確')
                    ->default(false),
                Forms\Components\TextInput::make('time_taken')
                    ->label('答題時間（秒）')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(300),
                Forms\Components\DateTimePicker::make('answered_at')
                    ->label('答題時間')
                    ->default(now()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('room.name')
                    ->label('房間')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('member.name')
                    ->label('玩家')
                    ->description(fn ($record) => $record->member->email ?? '')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('question.question')
                    ->label('題目')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('user_answer')
                    ->label('玩家答案')
                    ->limit(30),
                Tables\Columns\IconColumn::make('is_correct')
                    ->label('正確性')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('time_taken')
                    ->label('答題時間')
                    ->formatStateUsing(fn ($state) => $state ? "{$state}秒" : '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('answered_at')
                    ->label('答題時間')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('建立時間')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('room_id')
                    ->label('房間')
                    ->relationship('room', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('玩家')
                    ->relationship('member', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_correct')
                    ->label('正確性'),
                Tables\Filters\Filter::make('time_taken')
                    ->form([
                        Forms\Components\TextInput::make('min_time')
                            ->label('最少時間（秒）')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\TextInput::make('max_time')
                            ->label('最多時間（秒）')
                            ->numeric()
                            ->minValue(0),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_time'],
                                fn (Builder $query, $minTime): Builder => $query->where('time_taken', '>=', $minTime),
                            )
                            ->when(
                                $data['max_time'],
                                fn (Builder $query, $maxTime): Builder => $query->where('time_taken', '<=', $maxTime),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListGameRecords::route('/'),
            'create' => Pages\CreateGameRecord::route('/create'),
            'view' => Pages\ViewGameRecord::route('/{record}'),
            'edit' => Pages\EditGameRecord::route('/{record}/edit'),
        ];
    }
}
