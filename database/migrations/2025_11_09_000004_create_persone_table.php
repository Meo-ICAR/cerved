<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('codice_fiscale', 16)->unique();
            $table->string('nome')->nullable();
            $table->string('cognome')->nullable();
            $table->string('sesso', 1)->nullable();
            $table->date('data_nascita')->nullable();
            $table->string('comune_nascita')->nullable();
            $table->string('provincia_nascita', 2)->nullable();
            $table->string('nazione_nascita', 3)->default('ITA');
            $table->string('indirizzo_residenza')->nullable();
            $table->string('cap_residenza', 5)->nullable();
            $table->string('comune_residenza')->nullable();
            $table->string('provincia_residenza', 2)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email')->nullable();
            $table->timestamp('ultimo_aggiornamento_cerved')->nullable();
            $table->json('dati_anagrafici_completi')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('cognome');
            $table->index('nome');
            $table->index('codice_fiscale');
            $table->index('data_nascita');
            $table->index('comune_nascita');
            $table->index('provincia_nascita');

            // Foreign keys
            $table->foreign('provincia_nascita')
                  ->references('sigla')
                  ->on('provincie')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            $table->foreign('provincia_residenza')
                  ->references('sigla')
                  ->on('provincie')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('people');
    }
};
