<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeopleTable extends Migration
{
    public function up()
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cognome');
            $table->string('codice_fiscale', 16)->unique();
            $table->string('sesso', 1)->nullable();
            $table->date('data_nascita')->nullable();
            $table->string('luogo_nascita')->nullable();
            $table->string('provincia_nascita', 2)->nullable();
            $table->string('stato_nascita')->default('Italia')->nullable();
            
            // Contact information
            $table->string('telefono', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('pec')->nullable();
            
            // Additional info
            $table->string('stato_civile', 20)->nullable();
            $table->string('professione')->nullable();
            
            // Company relationship (if the person is associated with a company)
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            
            // Timestamps and soft deletes
            $table->timestamp('ultimo_aggiornamento_cerved')->nullable();
            $table->json('dati_aggiuntivi')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['cognome', 'nome']);
            $table->index('codice_fiscale');
        });
    }

    public function down()
    {
        Schema::dropIfExists('people');
    }
}
