<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('comuni', function (Blueprint $table) {
            $table->id();
            $table->string('codice_istat', 6)->unique();
            $table->string('nome');
            $table->string('nome_straniero')->nullable();
            $table->string('codice_provincia', 3);
            $table->string('sigla_provincia', 2);
            $table->string('regione');
            $table->string('codice_regione', 2);
            $table->string('codice_belfiore', 4)->nullable();
            $table->string('codice_catastale', 4)->nullable();
            $table->string('cap', 5);
            $table->boolean('capoluogo')->default(false);
            $table->string('zona_geografica', 1);
            $table->string('zona_sismica', 1)->nullable();
            $table->string('zona_climatica', 1)->nullable();
            $table->string('grado_giuridico', 1)->nullable();
            $table->string('targa', 2)->nullable();
            $table->string('stato', 3)->default('ITA');
            $table->boolean('attivo')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index('nome');
            $table->index('codice_provincia');
            $table->index('sigla_provincia');
            $table->index('codice_istat');
            $table->index('codice_catastale');
            $table->index('cap');
            
            // Foreign key
            $table->foreign('sigla_provincia')
                  ->references('sigla')
                  ->on('provincie')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('comuni');
    }
};
