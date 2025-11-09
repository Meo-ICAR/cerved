<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('protesti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('azienda_id')->constrained('companies')->onDelete('cascade');
            $table->date('data_protesto');
            $table->decimal('importo', 12, 2);
            $table->string('tipo_effetto'); // Es. "Assegno", "Cambiale", "Pagherò"
            $table->string('numero_effetto', 50)->nullable();
            $table->string('camera_compenso', 100);
            $table->string('stato')->default('ATTIVO'); // ATTIVO, PAGATO, ANNULLATO
            $table->string('causale', 255)->nullable();
            $table->date('data_rilevamento')->nullable();
            $table->json('dettagli_aggiuntivi')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('azienda_id');
            $table->index('data_protesto');
            $table->index('stato');
            $table->index('tipo_effetto');
            $table->index('camera_compenso');
        });
    }

    public function down()
    {
        Schema::dropIfExists('protesti');
    }
};
