<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ApiUsage;
use Carbon\Carbon;

class FetchApiUsage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cerved:fetch-api-usage 
                            {--start-date= : Start date (YYYY-MM-DD)} 
                            {--end-date= : End date (YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch API usage statistics from Cerved API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiKey = env('CERVED_API_KEY');
        
        if (empty($apiKey)) {
            $this->error('CERVED_API_KEY is not set in .env file');
            return 1;
        }

        // Set date range (default to last 7 days if not specified)
        $endDate = $this->option('end-date') ? Carbon::parse($this->option('end-date')) : now();
        $startDate = $this->option('start-date') 
            ? Carbon::parse($this->option('start-date')) 
            : $endDate->copy()->subDays(6);

        $this->info("Fetching API usage from {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => '*/*',
                'apikey' => $apiKey,
            ])->get('https://api.cerved.com/cervedApi/v1/accounting/statistics', [
                'dataInizio' => $startDate->format('Y-m-d'),
                'dataFine' => $endDate->format('Y-m-d'),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Log the full response for debugging
                Log::debug('Cerved API Response:', [
                    'url' => $response->effectiveUri(),
                    'status' => $response->status(),
                    'headers' => $response->headers(),
                    'body' => $data
                ]);
                
                // Check if we have a valid response structure
                if (is_array($data)) {
                    // The API returns the products array directly, not nested under 'products' key
                    $this->processProducts($data, $startDate, $endDate);
                    $this->info('API usage data has been successfully saved.');
                    return 0;
                }
                
                $this->error('Invalid API response format. Expected an array of products.');
                $this->error('Response: ' . json_encode($data, JSON_PRETTY_PRINT));
                return 1;
            }

            $this->error('Failed to fetch API usage: ' . $response->status());
            $this->error('Response: ' . $response->body());
            return 1;

        } catch (\Exception $e) {
            $this->error('Error fetching API usage: ' . $e->getMessage());
            Log::error('Error fetching API usage', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Process products data and save to database
     */
    /**
     * Process products data and save to database
     */
    protected function processProducts(array $products, Carbon $startDate, Carbon $endDate)
    {
        if (empty($products)) {
            $this->warn('No products data found in the API response.');
            return;
        }

        $bar = $this->output->createProgressBar(count($products));
        $bar->start();

        $reportDate = now()->format('Y-m-d');
        $processedCount = 0;
        
        foreach ($products as $product) {
            try {
                // Skip if product structure is invalid
                if (!is_array($product) || !isset($product['product'])) {
                    $this->warn("\nSkipping invalid product entry: " . json_encode($product));
                    continue;
                }
                
                // Ensure statistics is an array
                $statistics = $product['statistics'] ?? [];
                if (!is_array($statistics)) {
                    $this->warn("\nSkipping product '{$product['product']}' - invalid statistics format");
                    continue;
                }

                // Group statistics by status code
                $statsByStatus = [];
                foreach ($product['statistics'] as $stat) {
                    $status = $stat['status'] ?? 'unknown';
                    if (!isset($statsByStatus[$status])) {
                        $statsByStatus[$status] = [
                            'status' => $status,
                            'count' => 0,
                            'apps' => []
                        ];
                    }
                    
                    $appKey = ($stat['developer'] ?? 'unknown') . '::' . ($stat['app'] ?? 'unknown');
                    $statsByStatus[$status]['count'] += (int)($stat['count'] ?? 0);
                    
                    if (!isset($statsByStatus[$status]['apps'][$appKey])) {
                        $statsByStatus[$status]['apps'][$appKey] = [
                            'developer' => $stat['developer'] ?? 'unknown',
                            'app' => $stat['app'] ?? 'unknown',
                            'count' => 0
                        ];
                    }
                    $statsByStatus[$status]['apps'][$appKey]['count'] += (int)($stat['count'] ?? 0);
                }

                // Convert to array values for JSON storage
                $statsData = [
                    'total_requests' => array_sum(array_column($statsByStatus, 'count')),
                    'by_status' => array_values($statsByStatus)
                ];

                // Save to database
                ApiUsage::updateOrCreate(
                    [
                        'product' => $product['product'],
                        'report_date' => $reportDate,
                    ],
                    [
                        'statistics' => $statsData,
                    ]
                );
                
                $bar->advance();
                
            } catch (\Exception $e) {
                $this->warn("\nError processing product " . ($product['product'] ?? 'unknown') . ": " . $e->getMessage());
                Log::error("Error processing product", [
                    'product' => $product['product'] ?? null,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $bar->finish();
        $this->newLine(2);
    }
}
