<?php

namespace App\Services;

use App\Models\Question;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;
use League\Csv\Statement;

class QuestionImportService
{
    public function importFromCsv($file)
    {
        $results = [
            'success' => 0,
            'errors' => [],
            'total' => 0,
        ];

        try {
            $csv = Reader::createFromPath($file->getPathname(), 'r');
            $csv->setHeaderOffset(0);

            $records = Statement::create()->process($csv);
            $results['total'] = iterator_count($records);

            DB::beginTransaction();

            foreach ($records as $index => $record) {
                $rowNumber = $index + 2; // +2 because of 0-based index and header row

                try {
                    $this->validateRecord($record, $rowNumber);
                    $this->createQuestion($record);
                    $results['success']++;
                } catch (\Exception $e) {
                    $results['errors'][] = [
                        'row' => $rowNumber,
                        'message' => $e->getMessage(),
                        'data' => $record,
                    ];
                }
            }

            if (empty($results['errors'])) {
                DB::commit();
            } else {
                DB::rollBack();
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CSV import failed: ' . $e->getMessage());
            throw $e;
        }

        return $results;
    }

    private function validateRecord($record, $rowNumber)
    {
        $requiredFields = ['category_slug', 'type', 'question', 'correct_answer', 'options', 'difficulty'];
        
        foreach ($requiredFields as $field) {
            if (empty($record[$field])) {
                throw new \Exception("第 {$rowNumber} 行：缺少必填欄位 '{$field}'");
            }
        }

        // 驗證分類是否存在
        $category = Category::where('slug', $record['category_slug'])->first();
        if (!$category) {
            throw new \Exception("第 {$rowNumber} 行：分類 '{$record['category_slug']}' 不存在");
        }

        // 驗證題目類型
        if (!in_array($record['type'], ['choice', 'fill'])) {
            throw new \Exception("第 {$rowNumber} 行：題目類型必須是 'choice' 或 'fill'");
        }

        // 驗證難度
        if (!in_array($record['difficulty'], ['easy', 'medium', 'hard'])) {
            throw new \Exception("第 {$rowNumber} 行：難度必須是 'easy'、'medium' 或 'hard'");
        }

        // 驗證選項格式
        $options = json_decode($record['options'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("第 {$rowNumber} 行：選項格式錯誤，必須是有效的 JSON 陣列");
        }

        if (!is_array($options) || count($options) < 2) {
            throw new \Exception("第 {$rowNumber} 行：選項至少需要 2 個");
        }

        if (count($options) > 5) {
            throw new \Exception("第 {$rowNumber} 行：選項最多只能有 5 個");
        }

        // 驗證正確答案是否在選項中
        if (!in_array($record['correct_answer'], $options)) {
            throw new \Exception("第 {$rowNumber} 行：正確答案必須在選項中");
        }
    }

    private function createQuestion($record)
    {
        $category = Category::where('slug', $record['category_slug'])->first();
        $options = json_decode($record['options'], true);

        Question::create([
            'category_id' => $category->id,
            'type' => $record['type'],
            'question' => $record['question'],
            'correct_answer' => $record['correct_answer'],
            'options' => $options,
            'explanation' => $record['explanation'] ?? null,
            'difficulty' => $record['difficulty'],
            'is_active' => true,
        ]);
    }
} 