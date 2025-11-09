<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FabbricatiImportService;
use Illuminate\Support\Facades\File;

class ImportFabbricati extends Command
{
    protected $signature = 'import:fabbricati 
                            {json? : The JSON string or path to JSON file}
                            {--F|force : Force import even if the person already has properties}';

    protected $description = 'Import fabbricati and terreni data from JSON';

    protected $importService;

    public function __construct(FabbricatiImportService $importService)
    {
        parent::__construct();
        $this->importService = $importService;
    }

    public function handle()
    {
        $jsonInput = $this->argument('json');
        
        if (empty($jsonInput)) {
            $this->error('Please provide a JSON string or file path');
            return 1;
        }

        // Check if input is a file path
        if (file_exists($jsonInput)) {
            $jsonInput = file_get_contents($jsonInput);
        } elseif (file_exists(base_path($jsonInput))) {
            $jsonInput = file_get_contents(base_path($jsonInput));
        }

        try {
            $data = json_decode($jsonInput, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON: ' . json_last_error_msg());
            }

            // Check if the person already has properties
            $cf = $data['codiceFiscale'] ?? null;
            if (!$cf) {
                throw new \Exception('Missing codiceFiscale in JSON data');
            }

            $hasProperties = $this->personHasProperties($cf);
            
            if ($hasProperties && !$this->option('force')) {
                if (!$this->confirm("Person with CF {$cf} already has properties. Do you want to continue and add new properties?")) {
                    $this->info('Import cancelled.');
                    return 0;
                }
            }

            $result = $this->importService->importFromJson($data);
            
            $this->info(sprintf(
                'Successfully imported for person with CF: %s',
                $result['person']->codice_fiscale
            ));
            
            $this->info(sprintf(
                ' - Fabbricati: %d',
                $result['total_fabbricati']
            ));
            
            $this->info(sprintf(
                ' - Terreni: %d',
                $result['total_terreni']
            ));
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error importing data: ' . $e->getMessage());
            if (app()->environment('local')) {
                $this->error($e->getTraceAsString());
            }
            return 1;
        }
    }

    protected function personHasProperties($codiceFiscale)
    {
        return \App\Models\Fabbricato::whereHas('person', function($q) use ($codiceFiscale) {
            $q->where('codice_fiscale', $codiceFiscale);
        })->exists() || 
        \App\Models\Terreno::whereHas('person', function($q) use ($codiceFiscale) {
            $q->where('codice_fiscale', $codiceFiscale);
        })->exists();
    }
}
