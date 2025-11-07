<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_soggetto')->unique();
            $table->string('denominazione');
            $table->string('codice_fiscale')->nullable();
            $table->string('partita_iva')->nullable();
            
            // Dati Attività
            $table->string('codice_ateco')->nullable();
            $table->string('ateco')->nullable();
            $table->string('codice_ateco_infocamere')->nullable();
            $table->string('ateco_infocamere')->nullable();
            $table->string('codice_ateco_2025')->nullable();
            $table->string('ateco_2025')->nullable();
            $table->string('codice_ateco_infocamere_2025')->nullable();
            $table->string('ateco_infocamere_2025')->nullable();
            $table->string('codice_stato_attivita')->nullable();
            $table->boolean('flag_operativa')->default(true);
            $table->string('codice_rea')->nullable();
            $table->date('data_iscrizione_rea')->nullable();
            
            // Dati PA
            $table->boolean('is_ente')->default(false);
            $table->string('tipo_ente')->nullable();
            $table->boolean('is_fornitore')->default(false);
            $table->boolean('is_partecipata')->default(false);
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index('denominazione');
            $table->index('codice_fiscale');
            $table->index('partita_iva');
        });
    }

    public function down()
    {
        Schema::dropIfExists('companies');
    }
};
