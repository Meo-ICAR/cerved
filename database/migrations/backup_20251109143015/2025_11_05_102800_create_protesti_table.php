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
            $table->foreignId('persona_id')
                  ->constrained('persone')
                  ->onDelete('cascade');
            
            $table->string('tipo_protesto', 50);
            $table->date('data_evento')->nullable();
            $table->decimal('importo', 12, 2)->nullable();
            $table->string('camera_commercio', 100)->nullable();
            $table->json('dati_protesto_completi')->nullable();
            
            $table->timestamps();
            
            // Indici per le ricerche comuni
            $table->index('persona_id');
            $table->index('tipo_protesto');
            $table->index('data_evento');
        });
    }

    public function down()
    {
        Schema::dropIfExists('protesti');
    }
};
