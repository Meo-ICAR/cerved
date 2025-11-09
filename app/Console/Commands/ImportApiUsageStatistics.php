<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportApiUsageStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:import-usage-stats {jsonData} {--date= : The report date (YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import API usage statistics from JSON data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $jsonData = $this->argument('jsonData');
        $reportDate = $this->option('date') ? Carbon::parse($this->option('date')) : now();

        try {
            $data = json_decode($jsonData, true, 512, JSON_THROW_ON_ERROR);
            
            if (!is_array($data)) {
                $this->error('Invalid JSON data. Expected an array of product statistics.');
                return 1;
            }

            $records = [];
            foreach ($data as $productData) {
                if (!isset($productData['product']) || !isset($productData['statistics'])) {
                    $this->warn('Skipping invalid product data: ' . json_encode($productData));
                    continue;
                }

                $records[] = [
                    'product' => $productData['product'],
                    'statistics' => json_encode($productData['statistics']),
                    'report_date' => $reportDate->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (empty($records)) {
                $this->warn('No valid records found to import.');
                return 0;
            }

            // Use transaction for data integrity
            DB::beginTransaction();
            
            try {
                // Delete existing records for the same date to avoid duplicates
                DB::table('api_usages')
                    ->where('report_date', $reportDate->toDateString())
                    ->delete();
                
                // Insert new records
                DB::table('api_usages')->insert($records);
                
                DB::commit();
                $this->info(sprintf('Successfully imported %d product statistics for date: %s', 
                    count($records), 
                    $reportDate->toDateString()
                ));
                return 0;
                
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error('Failed to import data: ' . $e->getMessage());
                return 1;
            }

        } catch (\JsonException $e) {
            $this->error('Invalid JSON data: ' . $e->getMessage());
            return 1;
        }
    }
}
