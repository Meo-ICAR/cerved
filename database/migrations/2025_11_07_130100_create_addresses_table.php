<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
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
            
            // Indexes
            $table->index(['addressable_type', 'addressable_id']);
            $table->index('codice_comune_istat');
            $table->index('sigla_provincia');
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('addresses');
    }
};
