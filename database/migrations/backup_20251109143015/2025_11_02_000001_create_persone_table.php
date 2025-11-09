<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersoneTable extends Migration
{
    public function up()
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('codice_fiscale', 16)->unique();
            $table->string('nome')->nullable();
            $table->string('cognome')->nullable();
            $table->date('data_nascita')->nullable();
            $table->string('comune_nascita')->nullable();
            $table->string('provincia_nascita', 2)->nullable();
            $table->timestamp('ultimo_aggiornamento_cerved')->nullable();
            $table->json('dati_anagrafici_completi')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('persone');
    }
}
