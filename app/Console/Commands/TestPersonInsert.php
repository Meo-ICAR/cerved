<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Person;
use Illuminate\Support\Facades\DB;

class TestPersonInsert extends Command
{
    protected $signature = 'test:person-insert';
    protected $description = 'Test inserting a person record';

    public function handle()
    {
        DB::enableQueryLog();

        try {
            $person = Person::updateOrCreate(
                ['codice_fiscale' => 'MEOPGS64D04F839I'],
                [
                    'nome' => 'PIER GIUSEPPE',
                    'cognome' => 'MEO',
                    'data_nascita' => '04-04-1964',
                    'ultimo_aggiornamento_cerved' => now(),
                    'dati_anagrafici_completi' => ['test' => 'test']
                ]
            );
            
            $this->info("Person created/updated successfully!");
            
            $this->info("\nQuery Log:");
            $this->line(json_encode(DB::getQueryLog(), JSON_PRETTY_PRINT));
            
            // Verify the record was inserted
            $inserted = Person::where('codice_fiscale', 'MEOPGS64D04F839I')->first();
            
            $this->info("\nInserted Record:");
            $this->line(json_encode($inserted ? $inserted->toArray() : 'No record found', JSON_PRETTY_PRINT));
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $this->error("File: " . $e->getFile() . ":" . $e->getLine());
            $this->error("Trace: " . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
