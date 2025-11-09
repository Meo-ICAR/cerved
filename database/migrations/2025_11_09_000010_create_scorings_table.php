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
            $table->foreignId('azienda_id')->constrained('companies')->onDelete('cascade');
            $table->date('data_rilevamento');
            $table->string('tipo_scoring');
            $table->decimal('valore', 10, 2);
            $table->string('classe_rischio', 10)->nullable();
            $table->string('descrizione_classe')->nullable();
            $table->decimal('prob_default_12mesi', 5, 2)->nullable();
            $table->decimal('prob_default_36mesi', 5, 2)->nullable();
            $table->json('dettagli_aggiuntivi')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('azienda_id');
            $table->index('data_rilevamento');
            $table->index('tipo_scoring');
            $table->index('classe_rischio');
            
            // Unique constraint
            $table->unique(['azienda_id', 'tipo_scoring', 'data_rilevamento'], 'unique_scoring');
        });
    }

    public function down()
    {
        Schema::dropIfExists('scorings');
    }
};
