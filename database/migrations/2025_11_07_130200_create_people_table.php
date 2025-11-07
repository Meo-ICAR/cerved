<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_soggetto')->unique();
            $table->string('nome');
            $table->string('cognome');
            $table->string('codice_fiscale')->unique();
            $table->date('data_nascita')->nullable();
            
            // Indexes
            $table->index('cognome');
            $table->index('codice_fiscale');
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('people');
    }
};
