<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bilanci', function (Blueprint $table) {
            $table->id();
            $table->foreignId('azienda_id')->constrained('aziende')->onDelete('cascade');
            $table->year('anno');
            $table->date('data_chiusura');
            $table->boolean('esercizio_chiuso')->default(true);
            $table->string('valuta', 3)->default('EUR');
            $table->decimal('fatturato', 15, 2)->nullable();
            $table->decimal('utile_perdita', 15, 2)->nullable();
            $table->decimal('patrimonio_netto', 15, 2)->nullable();
            $table->decimal('attivo_circolante', 15, 2)->nullable();
            $table->decimal('totale_attivo', 15, 2)->nullable();
            $table->decimal('totale_passivo', 15, 2)->nullable();
            $table->json('dati_completi')->nullable();
            $table->timestamps();
            
            $table->unique(['azienda_id', 'anno']);
            $table->index(['azienda_id', 'esercizio_chiuso']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('bilanci');
    }
};
