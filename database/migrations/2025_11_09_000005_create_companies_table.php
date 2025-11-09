<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            // Primary key
            $table->id();
            
            // Company identification
            $table->unsignedBigInteger('id_soggetto')->unique();
            $table->string('denominazione');
            $table->string('codice_fiscale', 16)->nullable()->index();
            $table->string('partita_iva', 13)->nullable()->index();
            
            // ATECO codes and descriptions
            $table->string('codice_ateco', 10)->nullable();
            $table->string('ateco')->nullable();
            $table->string('codice_ateco_infocamere', 10)->nullable();
            $table->string('ateco_infocamere')->nullable();
            $table->string('codice_ateco_2025', 10)->nullable();
            $table->string('ateco_2025')->nullable();
            $table->string('codice_ateco_infocamere_2025', 10)->nullable();
            $table->string('ateco_infocamere_2025')->nullable();
            
            // Company status and registration
            $table->string('codice_stato_attivita', 10)->nullable();
            $table->boolean('flag_operativa')->default(true);
            $table->string('codice_rea', 20)->nullable();
            $table->date('data_iscrizione_rea')->nullable();
            
            // Company type flags
            $table->boolean('is_ente')->default(false);
            $table->string('tipo_ente', 50)->nullable();
            $table->boolean('is_fornitore')->default(false);
            $table->boolean('is_partecipata')->default(false);
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index('denominazione', 'companies_denominazione_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
