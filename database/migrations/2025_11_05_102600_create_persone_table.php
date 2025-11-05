<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('persone', function (Blueprint $table) {
            $table->id();
            $table->string('codice_fiscale', 16)->unique();
            $table->string('nome', 100)->nullable();
            $table->string('cognome', 100)->nullable();
            $table->date('data_nascita')->nullable();
            $table->string('comune_nascita', 100)->nullable();
            $table->string('provincia_nascita', 2)->nullable();
            $table->dateTime('ultimo_aggiornamento_cerved')->nullable();
            $table->json('dati_anagrafici_completi')->nullable();
            $table->timestamps();
            
            // Indici per le ricerche comuni
            $table->index(['cognome', 'nome']);
            $table->index('codice_fiscale');
            $table->index('data_nascita');
        });
    }

    public function down()
    {
        Schema::dropIfExists('persone');
    }
};
