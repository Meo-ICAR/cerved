<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Services\CaricheService;
use Illuminate\Support\Facades\Log;

class FetchCaricheData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cerved:fetch-cariche
                            {--c|codice-fiscale= : Codice fiscale della persona}
                            {--i|id-soggetto= : ID soggetto della persona}
                            {--d|debug : Show debug information}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch cariche data from Cerved API';

    /**
     * The CaricheService instance.
     *
     * @var CaricheService
     */
    protected $caricheService;

    /**
     * Create a new command instance.
     *
     * @param CaricheService $caricheService
     * @return void
     */
    public function __construct(CaricheService $caricheService)
    {
        parent::__construct();
        $this->caricheService = $caricheService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $apiKey = env('CERVED_API_KEY');
        
        if (empty($apiKey)) {
            $this->error('CERVED_API_KEY is not set in .env file');
            return 1;
        }

        $codiceFiscale = $this->option('codice-fiscale');
        $idSoggetto = $this->option('id-soggetto');
        $debug = $this->option('debug');

        if (empty($codiceFiscale) && empty($idSoggetto)) {
            $this->error('You must provide either --codice-fiscale or --id-soggetto');
            return 1;
        }

        try {
            $params = [];
            if ($codiceFiscale) {
                $params['codiceFiscale'] = $codiceFiscale;
                $this->info("Fetching cariche for codice fiscale: {$codiceFiscale}");
            } else {
                $params['idSoggetto'] = $idSoggetto;
                $this->info("Fetching cariche for ID soggetto: {$idSoggetto}");
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => '*/*',
                'apikey' => $apiKey,
            ])->get('https://api.cerved.com/cervedApi/v1/company/cariche', $params);

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

                // Process the response
                $result = $this->caricheService->processCaricheResponse($data);

                $this->info('=== Processing Results ===');
                $this->line("People processed: " . ($result['people_processed'] ?? 0));
                $this->line("Cariche processed: " . ($result['cariche_processed'] ?? 0));
                
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
            Log::error('Error in FetchCaricheData', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
