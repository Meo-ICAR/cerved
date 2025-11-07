<?php

namespace App\Services;

use App\Models\Person;
use App\Models\Company;
use App\Models\Address;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ApiResponseHandler
{
    public function handleResponse(array $response): array
    {
        $result = [
            'people_processed' => 0,
            'companies_processed' => 0,
            'errors' => []
        ];

        DB::beginTransaction();

        try {
            // Check if this is a direct response with data array
            if (isset($response['data']) && is_array($response['data'])) {
                foreach ($response['data'] as $entity) {
                    try {
                        if (isset($entity['dati_anagrafici'])) {
                            $this->processPerson($entity);
                            $result['people_processed']++;
                        } elseif (isset($entity['dati_aziendali'])) {
                            $this->processCompany($entity);
                            $result['companies_processed']++;
                        }
                    } catch (\Exception $e) {
                        $entityId = $entity['dati_anagrafici']['id_soggetto'] ?? 
                                  ($entity['dati_aziendali']['id_soggetto'] ?? 'unknown');
                        
                        $result['errors'][] = [
                            'type' => isset($entity['dati_anagrafici']) ? 'person' : 'company',
                            'id' => $entityId,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ];
                        
                        Log::error('Error processing entity', [
                            'id' => $entityId,
                            'type' => isset($entity['dati_anagrafici']) ? 'person' : 'company',
                            'error' => $e->getMessage(),
                            'data' => $entity
                        ]);
                    }
                }
            }
            
            // Support for the older format with separate people/companies arrays
            if (!empty($response['people'])) {
                foreach ($response['people'] as $personData) {
                    try {
                        $this->processPerson($personData);
                        $result['people_processed']++;
                    } catch (\Exception $e) {
                        $result['errors'][] = [
                            'type' => 'person',
                            'id' => $personData['dati_anagrafici']['id_soggetto'] ?? 'unknown',
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ];
                        Log::error('Error processing person', [
                            'id' => $personData['dati_anagrafici']['id_soggetto'] ?? 'unknown',
                            'error' => $e->getMessage(),
                            'data' => $personData
                        ]);
                    }
                }
            }

        } catch (\Exception $e) {
            $result['errors'][] = [
                'type' => 'general',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ];
            Log::error('Error in ApiResponseHandler', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return $result;
    }

    protected function processPerson(array $personData): void
    {
        $datiAnagrafici = $personData['dati_anagrafici'];
        $indirizzo = $datiAnagrafici['indirizzo'] ?? null;
        $idSoggetto = $datiAnagrafici['id_soggetto'];

        // Create or update person
        $person = Person::updateOrCreate(
            ['id_soggetto' => $idSoggetto],
            [
                'nome' => $datiAnagrafici['nome'] ?? null,
                'cognome' => $datiAnagrafici['cognome'] ?? null,
                'denominazione' => $datiAnagrafici['denominazione'] ?? 
                                 (($datiAnagrafici['nome'] ?? '') . ' ' . ($datiAnagrafici['cognome'] ?? '')),
                'codice_fiscale' => $datiAnagrafici['codice_fiscale'] ?? null,
                'data_nascita' => $datiAnagrafici['dt_nascita'] ?? null,
            ]
        );

        // Add address if exists
        if ($indirizzo && is_array($indirizzo)) {
            $this->addAddressToPerson($person, $indirizzo, 'RESIDENZA');
        }
    }

    protected function addAddressToPerson(Person $person, array $addressData, string $tipoIndirizzo = 'RESIDENZA'): void
    {
        // Skip if address data is empty
        if (empty($addressData)) {
            return;
        }

        $person->addresses()->updateOrCreate(
            [
                'addressable_id' => $person->id,
                'addressable_type' => get_class($person),
                'tipo_indirizzo' => $tipoIndirizzo
            ],
            [
                'indirizzo' => $addressData['descrizione'] ?? $addressData['indirizzo'] ?? null,
                'cap' => $addressData['cap'] ?? null,
                'codice_comune' => $addressData['codice_comune'] ?? null,
                'comune' => $addressData['descrizione_comune'] ?? $addressData['comune'] ?? null,
                'codice_comune_istat' => $addressData['codice_comune_istat'] ?? null,
                'sigla_provincia' => $addressData['provincia'] ?? null,
                'provincia' => $addressData['descrizione_provincia'] ?? $addressData['provincia'] ?? null,
            ]
        );
    }
}
