<?php

namespace App\Console\Commands;

use App\Services\QuestionImportService;
use Illuminate\Console\Command;

class ImportQuestions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'questions:import {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import questions from CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');
        
        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info("Importing questions from: {$filePath}");
        
        try {
            $importService = new QuestionImportService();
            $results = $importService->importFromCsv($filePath);
            
            $this->info("Import completed!");
            $this->info("Total records: {$results['total']}");
            $this->info("Successfully imported: {$results['success']}");
            
            if (!empty($results['errors'])) {
                $this->warn("Errors found: " . count($results['errors']));
                foreach ($results['errors'] as $error) {
                    $this->error("Row {$error['row']}: {$error['message']}");
                }
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Import failed: " . $e->getMessage());
            return 1;
        }
    }
}
