<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\ApiResponseHandler;
use App\Models\LogApiCerved;
use Carbon\Carbon;

class FetchCervedEntityData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cerved:fetch-entity 
                            {search : Search term (e.g., tax code, company name, etc.)}
                            {--t|type=all : Type of search (all, person, company)}
                            {--d|debug : Show debug information}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch entity data from Cerved API';

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

        $searchTerm = $this->argument('search');
        $searchType = strtolower($this->option('type'));
        $debug = $this->option('debug');

        $this->info("Searching for: {$searchTerm} (Type: {$searchType})");

        // Log the API request
        $logData = [
            'endpoint' => 'entitySearch/live',
            'method' => 'GET',
            'request_headers' => json_encode([
                'Content-Type' => 'application/json',
                'Accept' => '*/*',
                'apikey' => '***REDACTED***' // Don't log the actual API key
            ]),
            'request_body' => json_encode(['testoricerca' => $searchTerm]),
            'search_type' => $searchType,
            'status_code' => null,
            'response_headers' => null,
            'response_body' => null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'execution_time_ms' => null,
            'error_message' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $startTime = microtime(true);
        $logEntry = null;

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => '*/*',
                'apikey' => $apiKey,
            ])->get('https://api.cerved.com/cervedApi/v1/entitySearch/live', [
                'testoricerca' => $searchTerm,
            ]);

            $endTime = microtime(true);
            $executionTime = round(($endTime - $startTime) * 1000); // Convert to milliseconds

            // Update log entry with response data
            $logData['status_code'] = $response->status();
            $logData['response_headers'] = json_encode($response->headers());
            $logData['response_body'] = $response->body();
            $logData['execution_time_ms'] = $executionTime;

            // Save the log entry
            $logEntry = LogApiCerved::create($logData);

            if ($debug) {
                $this->line('');
                $this->info('=== Request Details ===');
                $this->line('URL: ' . $response->effectiveUri());
                $this->line('Status: ' . $response->status());
                $this->line('Headers: ' . json_encode($response->headers(), JSON_PRETTY_PRINT));
                $this->line('Execution Time: ' . $executionTime . 'ms');
                $this->line('');
            }

            if ($response->successful()) {
                $data = $response->json();
                
                if ($debug) {
                    $this->info('=== Response Body ===');
                    $this->line(json_encode($data, JSON_PRETTY_PRINT));
                    $this->line('');
                }

                // Update log with success status
                if ($logEntry) {
                    $logEntry->update([
                        'is_success' => true,
                        'updated_at' => now()
                    ]);
                }

                // Process the response using our ApiResponseHandler
                $handler = new ApiResponseHandler();
                $result = $handler->handleResponse($data);

                $this->info('=== Processing Results ===');
                $this->line("People processed: " . ($result['people_processed'] ?? 0));
                $this->line("Companies processed: " . ($result['companies_processed'] ?? 0));
                
                if (!empty($result['errors'])) {
                    $this->warn("\nErrors encountered:");
                    foreach ($result['errors'] as $error) {
                        $this->line("- {$error['type']} (ID: {$error['id']}): {$error['error']}");
                    }
                }

                return 0;
            }

            $errorMessage = 'API request failed with status: ' . $response->status();
            $this->error($errorMessage);
            $this->line('Response: ' . $response->body());
            
            // Update log with error
            if ($logEntry) {
                $logEntry->update([
                    'is_success' => false,
                    'error_message' => $errorMessage,
                    'response_body' => $response->body(),
                    'updated_at' => now()
                ]);
            }
            
            return 1;

        } catch (\Exception $e) {
            $errorMessage = 'Error: ' . $e->getMessage();
            $this->error($errorMessage);
            
            // Log the error
            Log::error('Error in FetchCervedEntityData', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Update log with error if we have a log entry
            if (isset($logEntry)) {
                $logEntry->update([
                    'is_success' => false,
                    'error_message' => $errorMessage,
                    'updated_at' => now()
                ]);
            } else {
                // If we don't have a log entry yet, create one with the error
                $logData['status_code'] = 500;
                $logData['error_message'] = $errorMessage;
                $logData['is_success'] = false;
                LogApiCerved::create($logData);
            }
            
            return 1;
        }
    }
}
