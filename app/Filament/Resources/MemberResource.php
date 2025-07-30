<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Filament\Resources\MemberResource\RelationManagers;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationGroup = '網站管理';
    protected static ?string $navigationLabel = '會員管理';
    protected static ?string $modelLabel = '會員';
    protected static ?string $pluralModelLabel = '會員';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('姓名')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('信箱')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('password')
                    ->label('密碼')
                    ->password()
                    ->dehydrated(fn ($state): bool => !empty($state))
                    ->required(fn (string $context): bool => $context === 'create'),
                Forms\Components\FileUpload::make('avatar')
                    ->label('頭像')
                    ->image()
                    ->imageEditor()
                    ->directory('avatars')
                    ->columnSpanFull()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->downloadable()
                    ->openable()
                    ->getUploadedFileNameForStorageUsing(
                        fn($file): string => (string) str(Str::uuid7() . '.webp')
                    )
                    ->saveUploadedFileUsing(function ($file) {
                        $manager = new ImageManager(new Driver());
                        $image = $manager->read($file);
                        $image->cover(200, 200);
                        $filename = Str::uuid7()->toString() . '.webp';

                        if (!file_exists(storage_path('app/public/avatars'))) {
                            mkdir(storage_path('app/public/avatars'), 0755, true);
                        }

                        $image->toWebp(80)->save(storage_path('app/public/avatars/' . $filename));
                        return 'avatars/' . $filename;
                    })
                    ->deleteUploadedFileUsing(function ($file) {
                        if ($file) {
                            Storage::disk('public')->delete($file);
                        }
                    }),
                Forms\Components\TextInput::make('total_games')
                    ->label('總遊戲次數')
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('correct_answers')
                    ->label('正確答案數')
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('total_answers')
                    ->label('總答案數')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('姓名')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('信箱')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('頭像')
                    ->circular(),
                Tables\Columns\TextColumn::make('total_games')
                    ->label('總遊戲次數')
                    ->sortable(),
                Tables\Columns\TextColumn::make('correct_answers')
                    ->label('正確答案數')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_answers')
                    ->label('總答案數')
                    ->sortable(),
                Tables\Columns\TextColumn::make('accuracy')
                    ->label('正確率')
                    ->formatStateUsing(fn ($record) => $record->accuracy . '%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('註冊時間')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }
}
