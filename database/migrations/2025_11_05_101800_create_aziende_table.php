<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('aziende', function (Blueprint $table) {
            $table->id();
            $table->string('partita_iva', 11)->unique();
            $table->string('codice_fiscale', 16)->nullable()->index();
            $table->string('cerved_id')->nullable()->index();
            $table->string('ragione_sociale');
            $table->string('natura_giuridica')->nullable();
            $table->string('stato_attivita')->nullable();
            $table->string('codice_ateco', 10)->nullable();
            $table->string('provincia_rea', 2)->nullable();
            $table->dateTime('ultimo_aggiornamento_cerved')->nullable();
            $table->json('dati_anagrafici_completi')->nullable();
            $table->json('dati_societa_controllanti')->nullable();
            $table->timestamps();
            
            $table->index(['partita_iva', 'codice_fiscale']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('aziende');
    }
};
