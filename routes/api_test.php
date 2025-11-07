<?php

use Illuminate\Support\Facades\Route;
use App\Services\ApiResponseHandler;

Route::get('/test-person', function () {
    $response = [
        'peopleTotalNumber' => 1,
        'companiesTotalNumber' => 0,
        'companies' => [],
        'people' => [
            [
                'dati_anagrafici' => [
                    'id_soggetto' => 317150503,
                    'nome' => 'PIER GIUSEPPE',
                    'cognome' => 'MEO',
                    'denominazione' => 'MEO PIER GIUSEPPE',
                    'dt_nascita' => '04-04-1964',
                    'codice_fiscale' => 'MEOPGS64D04F839I',
                    'indirizzo' => [
                        'descrizione' => 'VIA SALVATORE QUASIMODO, 5',
                        'cap' => '80018',
                        'codice_comune' => 'NA048',
                        'descrizione_comune' => 'MUGNANO DI NAPOLI',
                        'codice_comune_istat' => '063048',
                        'provincia' => 'NA',
                        'descrizione_provincia' => 'NAPOLI'
                    ]
                ]
            ]
        ]
    ];

    $handler = new ApiResponseHandler();
    $result = $handler->handleResponse($response);
    
    // Get the created person
    $person = \App\Models\Person::with('addresses')->find(317150503);
    
    return [
        'processing_result' => $result,
        'person' => $person,
        'address' => $person->addresses->first()
    ];
});
