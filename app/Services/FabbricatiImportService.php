<?php

namespace App\Services;

use App\Models\Person;
use App\Models\Fabbricato;
use App\Models\Terreno;
use App\Models\Possesso;
use Illuminate\Support\Facades\DB;

class FabbricatiImportService
{
    public function importFromJson($jsonData)
    {
        // If the input is a JSON string, decode it
        $data = is_string($jsonData) ? json_decode($jsonData, true) : $jsonData;

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON data provided: ' . json_last_error_msg());
        }

        // Start a database transaction
        return DB::transaction(function () use ($data) {
            // Find or create the person
            $person = Person::where('codice_fiscale', $data['codiceFiscale'])->first();

            if (!$person) {
                // Create a new person if not found
                $person = new Person([
                    'codice_fiscale' => $data['codiceFiscale'],
                    'nome' => $data['nome'] ?? null,
                    'cognome' => $data['cognome'] ?? null,
                    // Add other default fields as needed
                ]);
                $person->save();
            }

            $importedFabbricati = [];
            $importedTerreni = [];

            // Process fabbricati if they exist
            if (isset($data['fabbricati']) && is_array($data['fabbricati'])) {
                foreach ($data['fabbricati'] as $fabbricatoData) {
                    $importedFabbricati[] = $this->importFabbricato($person->id, $fabbricatoData);
                }
            }

            // Process terreni if they exist
            if (isset($data['terreni']) && is_array($data['terreni'])) {
                foreach ($data['terreni'] as $terrenoData) {
                    $importedTerreni[] = $this->importTerreno($person->id, $terrenoData);
                }
            }

            return [
                'person' => $person,
                'fabbricati' => $importedFabbricati,
                'terreni' => $importedTerreni,
                'total_fabbricati' => count($importedFabbricati),
                'total_terreni' => count($importedTerreni),
            ];
        });
    }

    protected function importFabbricato($personaId, array $fabbricatoData)
    {
        // Ensure the province exists
        $provincia = $this->ensureProvinceExists($fabbricatoData['codiceProvincia'] ?? null);

        // Handle piano field - convert 'T' to null if needed
        $piano = $this->normalizePiano($fabbricatoData['piano'] ?? null);

        // Create or update the fabbricato
        $fabbricato = Fabbricato::updateOrCreate(
            ['codice_immobile' => $fabbricatoData['idImmobile']],
            [
                'persona_id' => $personaId,
                'classe' => $fabbricatoData['classe'] ?? null,
                'codice_comune' => $fabbricatoData['codiceComune'] ?? null,
                'codice_belfiore' => $fabbricatoData['codiceBelfiore'] ?? null,
                'descrizione_comune' => $fabbricatoData['descrizioneComune'] ?? null,
                'codice_provincia' => $provincia,
                'foglio' => $fabbricatoData['foglio'] ?? null,
                'particella' => $fabbricatoData['particella'] ?? null,
                'subalterno' => $fabbricatoData['subalterno'] ?? null,
                'indirizzo' => $fabbricatoData['indirizzo'] ?? null,
                'piano' => $piano,
                'sezione_urbana' => $fabbricatoData['sezioneUrbana'] ?? null,
                'codice_categoria' => $fabbricatoData['codiceCategoria'] ?? null,
                'descrizione_categoria' => $fabbricatoData['descrizioneCategoria'] ?? null,
                'unita_misura_consistenza' => $fabbricatoData['unitaMisuraConsistenza'] ?? null,
                'valore_consistenza' => $fabbricatoData['valoreConsistenza'] ?? null,
                'rendita' => $fabbricatoData['rendita'] ?? null,
                'stima' => $fabbricatoData['stimaFabbricato'] ?? null,
            ]
        );

        // Process possessi for fabbricato
        $this->processPossessi($fabbricato, $fabbricatoData['possessi'] ?? []);

        return $fabbricato;
    }

    protected function importTerreno($personaId, array $terrenoData)
    {
        // Ensure the province exists
        $provincia = $this->ensureProvinceExists($terrenoData['codiceProvincia'] ?? null);

        // Create or update the terreno
        $terreno = Terreno::updateOrCreate(
            ['codice_immobile' => $terrenoData['idImmobile']],
            [
                'persona_id' => $personaId,
                'classe' => $terrenoData['classe'] ?? null,
                'codice_comune' => $terrenoData['codiceComune'] ?? null,
                'codice_belfiore' => $terrenoData['codiceBelfiore'] ?? null,
                'descrizione_comune' => $terrenoData['descrizioneComune'] ?? null,
                'codice_provincia' => $provincia,
                'foglio' => $terrenoData['foglio'] ?? null,
                'particella' => $terrenoData['particella'] ?? null,
                'sezione_censuaria' => $terrenoData['sezioneCensuaria'] ?? null,
                'codice_porzione' => $terrenoData['codicePorzione'] ?? null,
                'descrizione_qualita' => $terrenoData['descrizioneQualita'] ?? null,
                'superficie_ettari' => $terrenoData['superficieEttari'] ?? 0,
                'superficie_are' => $terrenoData['superficieAre'] ?? 0,
                'superficie_centiare' => $terrenoData['superficieCentiare'] ?? 0,
                'rendita_dominicale' => $terrenoData['renditaDominicale'] ?? null,
                'rendita_agraria' => $terrenoData['renditaAgraria'] ?? null,
            ]
        );

        // Process possessi for terreno
        $this->processPossessi($terreno, $terrenoData['possessi'] ?? []);

        return $terreno;
    }

    protected function processPossessi($model, array $possessiData)
    {
        if (!empty($possessiData) && is_array($possessiData)) {
            foreach ($possessiData as $possessoData) {
                $model->possessi()->updateOrCreate(
                    [
                        'descrizione_titolo' => $possessoData['descrizioneTitolo'] ?? null,
                        'quota_orig' => $possessoData['quotaOrig'] ?? null,
                    ],
                    [
                        'titolarita_orig' => $possessoData['titolaritaOrig'] ?? null,
                        'percentuale_quota' => $possessoData['percentualeQuota'] ?? null,
                    ]
                );
            }
        }
    }

    protected function ensureProvinceExists($provinceCode)
    {
        if (!$provinceCode) {
            return null;
        }

        $province = DB::table('provincie')
            ->where('province_code', $provinceCode)
            ->first();

        if (!$province) {
            // Optionally create the province if it doesn't exist
            // You might want to add more fields or handle this differently
            DB::table('provincie')->insert([
                'province_code' => $provinceCode,
                'province_name' => 'Unknown', // You might want to fetch the actual name
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $provinceCode;
    }

    protected function normalizePiano($piano)
    {
        if ($piano === 'T' || $piano === 'Terra' || $piano === 'Piano Terra') {
            return '0';
        }
        if ($piano === 'S1' || $piano === 'Sotterraneo 1' || $piano === 'Interrato 1') {
            return '-1';
        }
        if ($piano === 'S2' || $piano === 'Sotterraneo 2' || $piano === 'Interrato 2') {
            return '-2';
        }
        // Remove non-numeric characters except for minus sign
        return preg_replace('/[^0-9-]/', '', $piano);
    }
}
