<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Filament\Resources\QuestionResource\RelationManagers;
use App\Models\Question;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationGroup = '網站管理';
    protected static ?string $navigationLabel = '題目管理';
    protected static ?string $modelLabel = '題目';
    protected static ?string $pluralModelLabel = '題目';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('category_id')
                    ->label('分類')
                    ->relationship('category', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('type')
                    ->label('題目類型')
                    ->options([
                        'choice' => '選擇題',
                        'fill' => '填空題',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('question')
                    ->label('題目內容')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('correct_answer')
                    ->label('正確答案')
                    ->required(),
                Forms\Components\Repeater::make('options')
                    ->label('選項')
                    ->schema([
                        Forms\Components\TextInput::make('option')
                            ->label('選項')
                            ->required(),
                    ])
                    ->required()
                    ->minItems(2)
                    ->maxItems(5),
                Forms\Components\Textarea::make('explanation')
                    ->label('解釋')
                    ->columnSpanFull(),
                Forms\Components\Select::make('difficulty')
                    ->label('難度')
                    ->options([
                        'easy' => '簡單',
                        'medium' => '中等',
                        'hard' => '困難',
                    ])
                    ->default('easy')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->label('是否啟用')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->label('分類')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('question')
                    ->label('題目')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('類型')
                    ->colors([
                        'primary' => 'choice',
                        'success' => 'fill',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'choice' => '選擇題',
                        'fill' => '填空題',
                    }),
                Tables\Columns\BadgeColumn::make('difficulty')
                    ->label('難度')
                    ->colors([
                        'success' => 'easy',
                        'warning' => 'medium',
                        'danger' => 'hard',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'easy' => '簡單',
                        'medium' => '中等',
                        'hard' => '困難',
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('狀態')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('建立時間')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('分類')
                    ->relationship('category', 'name'),
                Tables\Filters\SelectFilter::make('type')
                    ->label('類型')
                    ->options([
                        'choice' => '選擇題',
                        'fill' => '填空題',
                    ]),
                Tables\Filters\SelectFilter::make('difficulty')
                    ->label('難度')
                    ->options([
                        'easy' => '簡單',
                        'medium' => '中等',
                        'hard' => '困難',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('狀態'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('import')
                    ->label('匯入題目')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->url(static::getUrl('import'))
                    ->color('success'),
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
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
            'import' => Pages\QuestionImport::route('/import'),
        ];
    }
}
