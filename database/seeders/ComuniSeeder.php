<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ComuniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all JSON files from the public directory
        $files = glob(public_path('*.json'));

        $processed = 0;
        $skipped = 0;
        $skippedRecords = [];

        foreach ($files as $file) {
            $json = File::get($file);
            $comuni = json_decode($json, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning("Invalid JSON in file: " . basename($file));
                continue;
            }

            foreach ($comuni as $comune) {
                try {
                    // Check if comune already exists
                    $exists = DB::table('comuni')
                        ->where('istat_code_municipality', $comune['istat_code_municipality'])
                        ->exists();

                    if (!$exists) {
                        DB::table('comuni')->insert([
                            'belfiore_code' => $comune['belfiore_code'],
                            'istat_code_municipality' => $comune['istat_code_municipality'],
                            'istat_code_province' => $comune['istat_code_province'],
                            'municipality_code' => $comune['municipality_code'],
                            'municipality_description' => $comune['municipality_description'],
                            'province_code' => $comune['province_code'],
                            'zip_code' => $comune['zip_code'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $processed++;
                    } else {
                        $skippedRecords[] = [
                            'comune' => $comune['municipality_description'],
                            'istat_code' => $comune['istat_code_municipality'],
                            'reason' => 'Duplicate istat_code_municipality'
                        ];
                        $skipped++;
                    }
                } catch (\Exception $e) {
                    $errorMessage = $e->getMessage();
                    $skippedRecords[] = [
                        'comune' => $comune['municipality_description'] ?? 'Unknown',
                        'istat_code' => $comune['istat_code_municipality'] ?? 'Unknown',
                        'reason' => $errorMessage
                    ];

                    $skipped++;
                }
            }
        }

        $this->command->info("Comuni seeding completed. Processed: {$processed}, Skipped: {$skipped}");

        if ($skipped > 0) {
            $this->command->warn("\nSkipped Records:");
            $headers = ['Comune', 'ISTAT Code', 'Reason'];
            $rows = array_map(function($record) {
                return [
                    $record['comune'],
                    $record['istat_code'],
                    $record['reason']
                ];
            }, $skippedRecords);

            $this->command->table($headers, $rows);
        }
    }
}
