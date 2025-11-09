<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sedi_aziende', function (Blueprint $table) {
            $table->id();
            $table->foreignId('azienda_id')->constrained('aziende')->onDelete('cascade');
            $table->string('tipo_sede');
            $table->string('indirizzo');
            $table->string('cap', 5);
            $table->string('comune');
            $table->string('provincia', 2);
            $table->string('regione')->nullable();
            $table->string('nazione')->default('Italia');
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('pec')->nullable();
            $table->boolean('is_legale')->default(false);
            $table->boolean('is_operativa')->default(true);
            $table->timestamps();
            
            $table->index(['azienda_id', 'is_legale']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('sedi_aziende');
    }
};
