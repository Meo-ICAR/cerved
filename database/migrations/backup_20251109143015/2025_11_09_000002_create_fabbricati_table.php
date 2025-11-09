<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFabbricatiTable extends Migration
{
    public function up()
    {
        Schema::create('fabbricati', function (Blueprint $table) {
            $table->bigInteger('id_immobile')->primary();
            $table->string('classe', 10);
            $table->string('codice_comune', 10);
            $table->string('codice_belfiore', 10);
            $table->string('descrizione_comune', 100);
            $table->string('codice_provincia', 2);
            $table->string('foglio', 10);
            $table->string('particella', 10);
            $table->integer('subalterno');
            $table->string('indirizzo', 255);
            $table->string('piano', 10)->nullable();
            $table->string('codice_categoria', 10);
            $table->string('descrizione_categoria', 100);
            $table->string('unita_misura_consistenza', 20);
            $table->decimal('valore_consistenza', 12, 2);
            $table->decimal('rendita', 12, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fabbricati');
    }
}
