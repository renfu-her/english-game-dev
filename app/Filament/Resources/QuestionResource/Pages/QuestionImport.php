<?php

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Filament\Resources\QuestionResource;
use App\Services\QuestionImportService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class QuestionImport extends Page
{
    protected static string $resource = QuestionResource::class;

    protected static string $view = 'filament.resources.question-resource.pages.question-import';

    protected static ?string $title = '匯入題目';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('csv_file')
                    ->label('CSV 檔案')
                    ->acceptedFileTypes(['text/csv'])
                    ->required()
                    ->helperText('請上傳包含題目資料的 CSV 檔案')
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function import(): void
    {
        $data = $this->form->getState();
        
        if (!isset($data['csv_file'])) {
            Notification::make()
                ->title('錯誤')
                ->body('請選擇要匯入的 CSV 檔案')
                ->danger()
                ->send();
            return;
        }

        try {
            $importService = new QuestionImportService();
            $results = $importService->importFromCsv($data['csv_file']);

            if (empty($results['errors'])) {
                Notification::make()
                    ->title('匯入成功')
                    ->body("成功匯入 {$results['success']} 筆題目")
                    ->success()
                    ->send();

                $this->redirect(QuestionResource::getUrl('index'));
            } else {
                $errorMessage = "匯入完成，但有錯誤：\n";
                foreach ($results['errors'] as $error) {
                    $errorMessage .= "第 {$error['row']} 行：{$error['message']}\n";
                }

                Notification::make()
                    ->title('匯入完成，但有錯誤')
                    ->body($errorMessage)
                    ->warning()
                    ->send();
            }

        } catch (\Exception $e) {
            Notification::make()
                ->title('匯入失敗')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
