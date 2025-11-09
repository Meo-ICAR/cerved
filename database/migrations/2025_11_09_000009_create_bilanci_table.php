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
            $table->foreignId('azienda_id')->constrained('companies')->onDelete('cascade');
            $table->year('anno');
            $table->date('data_chiusura');
            $table->boolean('esercizio_chiuso')->default(true);
            $table->string('valuta', 3)->default('EUR');
            
            // Dati di sintesi
            $table->decimal('fatturato', 15, 2)->default(0);
            $table->decimal('utile_perdita', 15, 2)->default(0);
            $table->decimal('patrimonio_netto', 15, 2)->default(0);
            $table->decimal('attivo_circolante', 15, 2)->default(0);
            $table->decimal('totale_attivo', 15, 2)->default(0);
            $table->decimal('totale_passivo', 15, 2)->default(0);
            
            // Dati aggiuntivi
            $table->decimal('margine_operativo', 15, 2)->nullable();
            $table->decimal('ebitda', 15, 2)->nullable();
            $table->decimal('ebit', 15, 2)->nullable();
            $table->decimal('indice_autonomia_finanziaria', 10, 2)->nullable();
            $table->decimal('roe', 10, 2)->nullable();
            $table->decimal('roi', 10, 2)->nullable();
            $table->decimal('ros', 10, 2)->nullable();
            
            // Dati completi in formato JSON
            $table->json('dati_completi')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('azienda_id');
            $table->index('anno');
            $table->index('data_chiusura');
            
            // Unique constraint
            $table->unique(['azienda_id', 'anno']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('bilanci');
    }
};
