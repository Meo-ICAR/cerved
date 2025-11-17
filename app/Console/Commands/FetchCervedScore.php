<?php

namespace App\Console\Commands;

use App\Models\Report;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchCervedScore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cerved:fetch-score {piva : The P.IVA to fetch score for} {--report= : The ID of the report to update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Cerved score for a specific P.IVA and save to report';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $piva = $this->argument('piva');
        $reportId = $this->option('report');

        // Validate P.IVA format (simple check for 11 digits)
        if (!preg_match('/^\d{11}$/', $piva)) {
            $this->error('Invalid P.IVA format. Please provide a valid 11-digit P.IVA.');
            return 1;
        }

        // If no report ID is provided, try to find an existing report with the same P.IVA
        $report = null;
        if ($reportId) {
            $report = Report::find($reportId);
            if (!$report) {
                $this->error("Report with ID {$reportId} not found.");
                return 1;
            }
        } else {
            $report = Report::where('piva', $piva)->first();
            if ($report) {
                $this->info("Found existing report ID: {$report->id} for P.IVA: {$piva}");
            } else {
                $this->info("No existing report found for P.IVA: {$piva}, a new one will be created.");
            }
        }

        $apiKey = env('CERVED_API_KEY');
        if (!$apiKey) {
            $this->error('CERVED_API_KEY is not set in .env file');
            return 1;
        }

        $url = "https://api.cerved.com/cervedApi/v1.1/score/impresa/corporate/ECOFIN?codice_fiscale={$piva}";

        $this->info("Fetching data from Cerved API for P.IVA: {$piva}");

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => '*/*',
                'apikey' => $apiKey,
            ])->get($url);

            if ($response->successful()) {
                $data = $response->json();

                if (empty($data['scores'])) {
                    $this->warn('No score data found for the provided P.IVA.');
                    return 0;
                }

                $score = $data['scores'][0]; // Get the first score

                $reportData = [
                    'id_soggetto' => $data['id_soggetto'],
                    'name' => $data['denominazione'],
                    'codice_score' => $score['codice_score'],
                    'descrizione_score' => $score['descrizione_score'],
                    'valore' => $score['valore'],
                    'categoria_codice' => $score['categoria_codice'],
                    'categoria_descrizione' => $score['categoria_descrizione'],
                    'piva' => $piva,
                ];

                if ($report) {
                    // Update existing report
                    $report->update($reportData);
                    $this->info("Successfully updated report ID: {$report->id}");
                } else {
                    // Create new report
                    $report = Report::create($reportData);
                    $this->info("Successfully created new report ID: {$report->id}");
                }

                $this->info('Score data:');
                $this->table(
                    ['Field', 'Value'],
                    [
                        ['ID Soggetto', $report->id_soggetto],
                        ['P.IVA', $report->piva],
                        ['Codice Score', $report->codice_score],
                        ['Descrizione', $report->descrizione_score],
                        ['Valore', $report->valore],
                        ['Categoria', $report->categoria_codice],
                        ['Descrizione Categoria', $report->categoria_descrizione],
                    ]
                );

                return 0;
            } else {
                $this->error('Error fetching data from Cerved API:');
                $this->error('Status: ' . $response->status());
                $this->error('Response: ' . $response->body());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('Exception occurred: ' . $e->getMessage());
            Log::error('Cerved API Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
