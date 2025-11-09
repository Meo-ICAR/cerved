<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cariche', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('persona_id');
            $table->string('tipologia_fonte', 1);
            $table->string('codice_carica', 10);
            $table->string('descrizione_carica');
            $table->date('data_inizio_carica');
            $table->date('data_fine_carica')->nullable();
            $table->text('poteri_persona')->nullable();
            $table->boolean('flag_rappresentante_ri')->default(false);
            $table->boolean('flag_carica_attiva')->default(true);
            $table->integer('importanza_carica')->default(1);
            $table->timestamps();

            // Foreign key to people table
            $table->foreign('persona_id')
                  ->references('id')
                  ->on('people')
                  ->onDelete('cascade');

            // Indexes
            $table->index('codice_carica');
            $table->index('flag_carica_attiva');
            $table->index('persona_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cariche');
    }
};
