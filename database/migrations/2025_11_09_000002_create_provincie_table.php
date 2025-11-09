<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('provincie', function (Blueprint $table) {
            $table->string('sigla', 2)->primary();
            $table->string('nome');
            $table->string('regione');
            $table->string('codice_istat', 3);
            $table->string('targa', 2)->nullable();
            $table->string('codice_ripartizione', 1);
            $table->string('ripartizione_geografica');
            $table->boolean('attiva')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index('nome');
            $table->index('regione');
            $table->index('codice_istat');
        });
    }

    public function down()
    {
        Schema::dropIfExists('provincie');
    }
};
