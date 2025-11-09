<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\Person;
use App\Models\Carica;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ImportSociCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-soci
                            {--id_soggetto=385648303 : ID Soggetto dell\'azienda}
                            {filepath : Path to the JSON file with soci data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import soci and their participations from JSON file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $idSoggetto = (int) $this->option('id_soggetto');
        $filepath = $this->argument('filepath');

        // Find the company
        $company = Company::where('id_soggetto', $idSoggetto)->first();

        if (!$company) {
            $this->error("Azienda con id_soggetto {$idSoggetto} non trovata.");
            return Command::FAILURE;
        }

        $this->info("Trovata azienda: {$company->denominazione} (ID: {$company->id}, ID Soggetto: {$company->id_soggetto})");

        // Read and parse the JSON file
        if (!File::exists($filepath)) {
            $this->error("File non trovato: {$filepath}");
            return Command::FAILURE;
        }

        $jsonData = json_decode(File::get($filepath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('File JSON non valido: ' . json_last_error_msg());
            return Command::FAILURE;
        }

        if (empty($jsonData['soci'])) {
            $this->warn('Nessun socio trovato nel file JSON.');
            return Command::SUCCESS;
        }

        $importedCount = 0;
        $updatedCount = 0;
        $errorCount = 0;

        foreach ($jsonData['soci'] as $socio) {
            try {
                // Start a database transaction
                DB::beginTransaction();

                try {
                    // Prepare person data
                    $personData = [
                        'id_soggetto' => $socio['id_soggetto'] ?? null,
                        'codice_fiscale' => $socio['codice_fiscale'],
                        'nome' => $socio['nome'] ?? null,
                        'cognome' => $socio['cognome'] ?? null,
                        'tipo_soggetto' => $socio['tipo_soggetto'] ?? null,
                        'ultimo_aggiornamento_cerved' => now(),
                    ];

                    // Check if person exists by codice_fiscale
                    $person = Person::where('codice_fiscale', $socio['codice_fiscale'])->first();

                    if ($person) {
                        // Update existing person
                        $person->update($personData);
                        $updatedCount++;
                        $this->info("Aggiornato socio: {$person->cognome} {$person->nome} (CF: {$person->codice_fiscale})");
                    } else {
                        // Create new person
                        $person = Person::create($personData);
                        $importedCount++;
                        $this->info("Importato nuovo socio: {$person->cognome} {$person->nome} (CF: {$person->codice_fiscale})");
                    }

                    // Handle partecipazioni
                    if (!empty($socio['partecipazioni'])) {
                        foreach ($socio['partecipazioni'] as $partecipazione) {
                            // Prepare carica data with new share-related fields
                            $caricaData = [
                                'persona_id' => $person->id,
                                'azienda_id' => $company->id,
                                'tipo_carica' => $this->mapRuoloToTipoCarica($partecipazione['ruolo'] ?? null),
                                'descrizione_carica' => $partecipazione['legame'] ?? 'Partecipazione societaria',
                                'data_inizio_carica' => !empty($partecipazione['data_inizio_legame']) ? 
                                    Carbon::createFromFormat('d-m-Y', $partecipazione['data_inizio_legame']) : null,
                                'numero_quote' => $partecipazione['numero_quote'] ?? null,
                                'valore_totale_quote' => $partecipazione['valore_totale_quote'] ?? null,
                                'quota_massima_societa' => $partecipazione['quota_massima_societa'] ?? null,
                                'percentuale_quota_partecipazione' => $partecipazione['percentuale_quota_partecipazione'] ?? null,
                                'tipo_diritto' => $partecipazione['tipo_diritto'] ?? null,
                                'dati_carica_completi' => $partecipazione, // Store complete participation data
                            ];

                            // Check if this carica already exists
                            $carica = Carica::where('persona_id', $person->id)
                                ->where('azienda_id', $company->id)
                                ->where('tipo_carica', $caricaData['tipo_carica'])
                                ->whereDate('data_inizio_carica', $caricaData['data_inizio_carica']?->format('Y-m-d'))
                                ->first();

                            if ($carica) {
                                $carica->update($caricaData);
                                $this->line("  - Aggiornata carica: {$caricaData['tipo_carica']}");
                            } else {
                                Carica::create($caricaData);
                                $this->line("  - Creata nuova carica: {$caricaData['tipo_carica']}");
                            }
                        }
                    }

                    // Commit the transaction if everything is successful
                    DB::commit();

                } catch (\Exception $e) {
                    // Rollback the transaction on error
                    DB::rollBack();
                    $errorCount++;
                    $this->error("Errore durante l'importazione del socio {$socio['codice_fiscale']}: " . $e->getMessage());
                    Log::error("Errore durante l'importazione del socio", [
                        'codice_fiscale' => $socio['codice_fiscale'] ?? null,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("Errore durante l'elaborazione del socio: " . $e->getMessage());
                Log::error("Errore durante l'elaborazione del socio", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $this->newLine();
        $this->info("Import completato:");
        $this->line("- Soci importati: {$importedCount}");
        $this->line("- Soci aggiornati: {$updatedCount}");
        $this->line("- Errori: {$errorCount}");

        return Command::SUCCESS;
    }

    /**
     * Map ruolo from JSON to tipo_carica
     */
    protected function mapRuoloToTipoCarica(?string $ruolo): string
    {
        return match ($ruolo) {
            'S' => 'Socio',
            'A' => 'Amministratore',
            'AD' => 'Amministratore Delegato',
            'C' => 'Consigliere',
            'P' => 'Presidente',
            'T' => 'Titolare',
            default => 'Socio',
        };
    }
}
