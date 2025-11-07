<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\ApiResponseHandler;

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

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => '*/*',
                'apikey' => $apiKey,
            ])->get('https://api.cerved.com/cervedApi/v1/entitySearch/live', [
                'testoricerca' => $searchTerm,
            ]);

            if ($debug) {
                $this->line('');
                $this->info('=== Request Details ===');
                $this->line('URL: ' . $response->effectiveUri());
                $this->line('Status: ' . $response->status());
                $this->line('Headers: ' . json_encode($response->headers(), JSON_PRETTY_PRINT));
                $this->line('');
            }

            if ($response->successful()) {
                $data = $response->json();
                
                if ($debug) {
                    $this->info('=== Response Body ===');
                    $this->line(json_encode($data, JSON_PRETTY_PRINT));
                    $this->line('');
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

            $this->error('API request failed with status: ' . $response->status());
            $this->line('Response: ' . $response->body());
            
            return 1;

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('Error in FetchCervedEntityData', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
