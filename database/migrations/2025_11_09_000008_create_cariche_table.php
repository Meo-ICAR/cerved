<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cariche', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->constrained('people')->onDelete('cascade');
            $table->foreignId('azienda_id')->constrained('companies')->onDelete('cascade');
            $table->string('tipo_carica'); // Es. "Amministratore Delegato"
            $table->string('descrizione_carica'); // Descrizione estesa da Cerved
            $table->date('data_inizio_carica')->nullable();
            $table->date('data_fine_carica')->nullable();
            $table->string('qualifica')->nullable();
            $table->string('codice_fiscale_rappresentato', 16)->nullable();
            $table->json('dati_carica_completi')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('persona_id');
            $table->index('azienda_id');
            $table->index('tipo_carica');
            $table->index('data_inizio_carica');
            $table->index('data_fine_carica');
            
            // Unique constraint to prevent duplicate roles
            $table->unique(['persona_id', 'azienda_id', 'tipo_carica', 'data_inizio_carica'], 'unique_carica');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cariche');
    }
};
