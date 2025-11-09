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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('addressable_type');
            $table->unsignedBigInteger('addressable_id');
            $table->string('tipo_indirizzo')->default('SEDE_LEGALE');
            $table->string('indirizzo');
            $table->string('cap', 5);
            $table->string('codice_comune');
            $table->string('comune');
            $table->string('codice_comune_istat');
            $table->string('sigla_provincia', 2);
            $table->string('provincia');
            $table->timestamps();

            // Indexes
            $table->index(['addressable_type', 'addressable_id'], 'addresses_addressable_type_addressable_id_index');
            $table->index('codice_comune_istat', 'addresses_codice_comune_istat_index');
            $table->index('sigla_provincia', 'addresses_sigla_provincia_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
