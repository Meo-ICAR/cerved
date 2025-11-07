<?php

namespace App\Services;

use App\Models\Person;
use App\Models\Carica;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CaricheService
{
    /**
     * Process the cariche data from the API response
     *
     * @param array $response
     * @return array
     */
    public function processCaricheResponse(array $response): array
    {
        $result = [
            'people_processed' => 0,
            'cariche_processed' => 0,
            'errors' => []
        ];

        if (empty($response['esponenti'])) {
            return $result;
        }

        DB::beginTransaction();

        try {
            foreach ($response['esponenti'] as $esponente) {
                try {
                    // Find or create the person
                    $person = Person::updateOrCreate(
                        ['codice_fiscale' => $esponente['codice_fiscale']],
                        [
                            'id_soggetto' => $esponente['id_soggetto'],
                            'nome' => $esponente['nome'] ?? null,
                            'cognome' => $esponente['cognome'] ?? null,
                            'tipo_soggetto' => $esponente['tipo_soggetto'] ?? 'P',
                        ]
                    );

                    $result['people_processed']++;

                    // Process cariche if they exist
                    if (!empty($esponente['cariche']) && is_array($esponente['cariche'])) {
                        foreach ($esponente['cariche'] as $caricaData) {
                            $this->processCarica($person->id, $caricaData);
                            $result['cariche_processed']++;
                        }
                    }
                } catch (\Exception $e) {
                    $result['errors'][] = [
                        'type' => 'person',
                        'id' => $esponente['id_soggetto'] ?? 'unknown',
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ];
                    Log::error('Error processing esponente', [
                        'id' => $esponente['id_soggetto'] ?? 'unknown',
                        'error' => $e->getMessage(),
                        'data' => $esponente
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            
            $result['errors'][] = [
                'type' => 'general',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ];
            
            Log::error('Error in CaricheService', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return $result;
    }

    /**
     * Process a single carica record
     *
     * @param int $personaId
     * @param array $caricaData
     * @return Carica
     */
    protected function processCarica(int $personaId, array $caricaData): Carica
    {
        $carica = Carica::updateOrCreate(
            [
                'persona_id' => $personaId,
                'codice_carica' => $caricaData['codice_carica'],
                'data_inizio_carica' => \Carbon\Carbon::createFromFormat('d-m-Y', $caricaData['data_inizio_carica'])
            ],
            [
                'tipologia_fonte' => $caricaData['tipologia_fonte'],
                'descrizione_carica' => $caricaData['descrizione_carica'],
                'poteri_persona' => $caricaData['poteri_persona'] ?? null,
                'flag_rappresentante_ri' => $caricaData['flag_rappresentante_ri'] ?? false,
                'importanza_carica' => $caricaData['importanza_carica'] ?? 1,
                'flag_carica_attiva' => $caricaData['flag_carica_attiva'] ?? true,
            ]
        );

        return $carica;
    }
}
