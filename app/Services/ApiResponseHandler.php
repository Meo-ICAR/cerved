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

        // Start a new database connection to avoid transaction issues
        $connection = DB::connection();
        $connection->beginTransaction();
        
        try {
            // Check if this is a direct response with data array
            if (isset($response['data']) && is_array($response['data'])) {
                $success = true;
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
            
            // Handle direct companies array in response
            if (!empty($response['companies']) && is_array($response['companies'])) {
                foreach ($response['companies'] as $companyData) {
                    try {
                        $this->processCompany($companyData);
                        $result['companies_processed']++;
                    } catch (\Exception $e) {
                        $entityId = $companyData['dati_anagrafici']['id_soggetto'] ?? 'unknown';
                        
                        $result['errors'][] = [
                            'type' => 'company',
                            'id' => $entityId,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ];
                        
                        Log::error('Error processing company', [
                            'id' => $entityId,
                            'error' => $e->getMessage(),
                            'data' => $companyData
                        ]);
                    }
                }
            }
            
            // Support for the older format with separate people/companies arrays
            if (!empty($response['people']) && is_array($response['people'])) {
                foreach ($response['people'] as $personData) {
                    try {
                        $this->processPerson($personData);
                        $result['people_processed']++;
                    } catch (\Exception $e) {
                        $entityId = $personData['dati_anagrafici']['id_soggetto'] ?? 'unknown';
                        
                        $result['errors'][] = [
                            'type' => 'person',
                            'id' => $entityId,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ];
                        
                        Log::error('Error processing person', [
                            'id' => $entityId,
                            'error' => $e->getMessage(),
                            'data' => $personData
                        ]);
                    }
                }
            }

        } catch (\Exception $e) {
            $connection->rollBack();
            
            $result['errors'][] = [
                'type' => 'general',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ];
            
            Log::error('Error in ApiResponseHandler', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'response' => $response
            ]);
        }

        // Commit the transaction if everything was successful
        if (!isset($success) || $success) {
            $connection->commit();
            Log::info('Transaction committed successfully');
        } else {
            $connection->rollBack();
            Log::warning('Transaction rolled back - no data was processed');
        }
        
        return $result;
    }

    protected function processPerson(array $personData): void
    {
        \Log::info('Processing person data:', $personData);
        
        try {
            $datiAnagrafici = $personData['dati_anagrafici'];
            $indirizzo = $datiAnagrafici['indirizzo'] ?? null;
            $idSoggetto = $datiAnagrafici['id_soggetto'];

            // Prepare the update data
            $updateData = [
                'nome' => $datiAnagrafici['nome'] ?? null,
                'cognome' => $datiAnagrafici['cognome'] ?? null,
                'denominazione' => $datiAnagrafici['denominazione'] ?? 
                                 (($datiAnagrafici['nome'] ?? '') . ' ' . ($datiAnagrafici['cognome'] ?? '')),
                'codice_fiscale' => $datiAnagrafici['codice_fiscale'] ?? null,
                'data_nascita' => $datiAnagrafici['dt_nascita'] ?? null,
                'ultimo_aggiornamento_cerved' => now(),
                'dati_anagrafici_completi' => $personData // Store the complete response
            ];

            \Log::info('Prepared update data:', $updateData);

            // Create or update person
            $person = Person::updateOrCreate(
                ['id_soggetto' => $idSoggetto],
                $updateData
            );

            \Log::info('Person saved successfully', ['person_id' => $person->id, 'id_soggetto' => $idSoggetto]);

            // Add address if exists
            if ($indirizzo && is_array($indirizzo)) {
                $this->addAddressToPerson($person, $indirizzo, 'RESIDENZA');
                \Log::info('Address added for person', ['person_id' => $person->id]);
            }
        } catch (\Exception $e) {
            \Log::error('Error processing person', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'person_data' => $personData
            ]);
            throw $e;
        }
    }

    protected function addAddressToPerson(Person $person, array $addressData, string $tipoIndirizzo = 'RESIDENZA'): void
    {
        // Skip if address data is empty
        if (empty($addressData)) {
            \Log::warning('Empty address data provided for person', ['person_id' => $person->id]);
            return;
        }

        \Log::info('Processing address for person', [
            'person_id' => $person->id,
            'address_data' => $addressData,
            'tipo_indirizzo' => $tipoIndirizzo
        ]);

        try {
            $address = $person->addresses()->updateOrCreate(
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

            \Log::info('Address saved successfully', [
                'address_id' => $address->id,
                'person_id' => $person->id,
                'tipo_indirizzo' => $tipoIndirizzo
            ]);
        } catch (\Exception $e) {
            \Log::error('Error saving address for person', [
                'person_id' => $person->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'address_data' => $addressData
            ]);
        }
    }

    /**
     * Process a company from the API response
     *
     * @param array $companyData
     * @return void
     */
    protected function processCompany(array $companyData): void
    {
        \Log::info('Processing company data:', $companyData);
        
        try {
            $datiAnagrafici = $companyData['dati_anagrafici'];
            $indirizzo = $datiAnagrafici['indirizzo'] ?? null;
            $idSoggetto = $datiAnagrafici['id_soggetto'];
            $partitaIva = $datiAnagrafici['partita_iva'] ?? $datiAnagrafici['codice_fiscale'] ?? null;

            // Prepare the update data
            $updateData = [
                'denominazione' => $datiAnagrafici['denominazione'] ?? null,
                'codice_fiscale' => $datiAnagrafici['codice_fiscale'] ?? null,
                'partita_iva' => $partitaIva,
                'ultimo_aggiornamento_cerved' => now(),
                'dati_anagrafici_completi' => $companyData // Store the complete response
            ];

            // Add dati_attivita if exists
            if (isset($companyData['dati_attivita'])) {
                $updateData['codice_ateco'] = $companyData['dati_attivita']['codice_ateco'] ?? null;
                $updateData['descrizione_ateco'] = $companyData['dati_attivita']['ateco'] ?? null;
                $updateData['codice_rea'] = $companyData['dati_attivita']['codice_rea'] ?? null;
                $updateData['data_iscrizione_rea'] = $companyData['dati_attivita']['data_iscrizione_rea'] ?? null;
            }

            \Log::info('Prepared company update data:', $updateData);

            // Create or update company
            $company = Company::updateOrCreate(
                ['id_soggetto' => $idSoggetto],
                $updateData
            );

            \Log::info('Company saved successfully', ['company_id' => $company->id, 'id_soggetto' => $idSoggetto]);

            // Add address if exists
            if ($indirizzo && is_array($indirizzo)) {
                $this->addAddressToCompany($company, $indirizzo, 'SEDE_LEGALE');
                \Log::info('Address added for company', ['company_id' => $company->id]);
            }
        } catch (\Exception $e) {
            \Log::error('Error processing company', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'company_data' => $companyData
            ]);
            throw $e;
        }
    }

    /**
     * Add an address to a company
     *
     * @param Company $company
     * @param array $addressData
     * @param string $tipoIndirizzo
     * @return void
     */
    protected function addAddressToCompany(Company $company, array $addressData, string $tipoIndirizzo = 'SEDE_LEGALE'): void
    {
        // Skip if address data is empty
        if (empty($addressData)) {
            \Log::warning('Empty address data provided for company', ['company_id' => $company->id]);
            return;
        }

        \Log::info('Processing address for company', [
            'company_id' => $company->id,
            'address_data' => $addressData,
            'tipo_indirizzo' => $tipoIndirizzo
        ]);

        try {
            $address = $company->addresses()->updateOrCreate(
                [
                    'addressable_id' => $company->id,
                    'addressable_type' => get_class($company),
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

            \Log::info('Company address saved successfully', [
                'address_id' => $address->id,
                'company_id' => $company->id,
                'tipo_indirizzo' => $tipoIndirizzo
            ]);
        } catch (\Exception $e) {
            \Log::error('Error saving address for company', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'address_data' => $addressData
            ]);
        }
    }
}
