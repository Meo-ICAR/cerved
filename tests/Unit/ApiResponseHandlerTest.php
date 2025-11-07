<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Person;
use App\Models\Address;
use App\Services\ApiResponseHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiResponseHandlerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_processes_person_response_correctly()
    {
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

        // Assert the result
        $this->assertEquals(1, $result['people_processed']);
        $this->assertEmpty($result['errors']);

        // Verify person was created
        $person = Person::where('id_soggetto', 317150503)->first();
        $this->assertNotNull($person);
        $this->assertEquals('PIER GIUSEPPE', $person->nome);
        $this->assertEquals('MEO', $person->cognome);
        $this->assertEquals('04-04-1964', $person->data_nascita->format('d-m-Y'));

        // Verify address was created
        $address = $person->addresses()->first();
        $this->assertNotNull($address);
        $this->assertEquals('VIA SALVATORE QUASIMODO, 5', $address->indirizzo);
        $this->assertEquals('80018', $address->cap);
        $this->assertEquals('MUGNANO DI NAPOLI', $address->comune);
        $this->assertEquals('NA', $address->sigla_provincia);
    }
}
