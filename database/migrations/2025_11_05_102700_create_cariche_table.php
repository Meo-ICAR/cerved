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
            $table->foreignId('persona_id')
                  ->constrained('persone')
                  ->onDelete('cascade');
                  
            $table->foreignId('azienda_id')
                  ->constrained('aziende')
                  ->onDelete('cascade');
            
            $table->string('tipo_carica', 100);
            $table->text('descrizione_carica');
            $table->date('data_inizio_carica')->nullable();
            $table->date('data_fine_carica')->nullable();
            $table->json('dati_carica_completi')->nullable();
            
            $table->timestamps();
            
            // Indici per le ricerche comuni
            $table->index(['persona_id', 'tipo_carica']);
            $table->index(['azienda_id', 'tipo_carica']);
            $table->index('data_inizio_carica');
            $table->index('data_fine_carica');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cariche');
    }
};
