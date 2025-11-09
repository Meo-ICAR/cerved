<?php

namespace App\Console\Commands;

use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImportTestAzienda extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-test-azienda {filepath : Path to the JSON test file} {--update : Update existing record if found}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import test Azienda data from JSON file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filepath = $this->argument('filepath');
        $shouldUpdate = $this->option('update');

        if (!File::exists($filepath)) {
            $this->error("File not found: {$filepath}");
            return Command::FAILURE;
        }

        $jsonData = json_decode(File::get($filepath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON file: ' . json_last_error_msg());
            return Command::FAILURE;
        }

        if (empty($jsonData['companies'])) {
            $this->error('No company data found in the JSON file');
            return Command::FAILURE;
        }

        $count = 0;

        foreach ($jsonData['companies'] as $company) {
            $datiAnagrafici = $company['dati_anagrafici'] ?? [];
            $datiAttivita = $company['dati_attivita'] ?? [];

            $aziendaData = [
                'id_soggetto' => $datiAnagrafici['id_soggetto'] ?? null,
                'partita_iva' => $datiAnagrafici['partita_iva'] ?? null,
                'codice_fiscale' => $datiAnagrafici['codice_fiscale'] ?? null,
                'denominazione' => $datiAnagrafici['denominazione'] ?? null,
                'codice_ateco' => $datiAttivita['codice_ateco'] ?? null,
                'ateco' => $datiAttivita['ateco'] ?? null,
                'codice_ateco_infocamere' => $datiAttivita['codice_ateco_infocamere'] ?? null,
                'ateco_infocamere' => $datiAttivita['ateco_infocamere'] ?? null,
                'codice_ateco_2025' => $datiAttivita['codice_ateco_2025'] ?? null,
                'ateco_2025' => $datiAttivita['ateco_2025'] ?? null,
                'codice_ateco_infocamere_2025' => $datiAttivita['codice_ateco_infocamere_2025'] ?? null,
                'ateco_infocamere_2025' => $datiAttivita['ateco_infocamere_2025'] ?? null,
                'codice_stato_attivita' => $datiAttivita['codice_stato_attivita'] ?? null,
                'flag_operativa' => $datiAttivita['flag_operativa'] ?? false,
                'codice_rea' => $datiAttivita['codice_rea'] ?? null,
                'data_iscrizione_rea' => !empty($datiAttivita['data_iscrizione_rea']) ?
                    Carbon::createFromFormat('d-m-Y', $datiAttivita['data_iscrizione_rea'])->format('Y-m-d') : null,
                'is_ente' => $company['dati_pa']['ente'] ?? false,
                'is_fornitore' => $company['dati_pa']['fornitore'] ?? false,
                'is_partecipata' => $company['dati_pa']['partecipata'] ?? false,
            ];

            $azienda = Company::where('partita_iva', $aziendaData['partita_iva'])->first();

            if ($azienda) {
                if (!$shouldUpdate) {
                    $this->warn("Azienda with P.IVA {$aziendaData['partita_iva']} already exists. Use --update to update it.");
                    continue;
                }

                $azienda->update($aziendaData);
                $this->info("Updated Azienda: {$azienda->denominazione} (P.IVA: {$azienda->partita_iva})");
            } else {
                $azienda = Company::create($aziendaData);
                $this->info("Created Azienda: {$azienda->denominazione} (P.IVA: {$azienda->partita_iva})");
            }

            $count++;
        }

        $this->info("\nImport completed. Processed {$count} companies.");
        return Command::SUCCESS;
    }
}
