<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('scorings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('azienda_id')->constrained('aziende')->onDelete('cascade');
            $table->dateTime('data_elaborazione');
            $table->integer('punteggio');
            $table->string('classe_di_rischio', 10);
            $table->float('probabile_fallimento', 5, 2)->nullable();
            $table->decimal('limite_credito_consigliato', 15, 2)->nullable();
            $table->json('fattori_rischio')->nullable();
            $table->json('dettagli_analisi')->nullable();
            $table->timestamps();
            
            $table->index(['azienda_id', 'data_elaborazione']);
            $table->index('classe_di_rischio');
        });
    }

    public function down()
    {
        Schema::dropIfExists('scorings');
    }
};
