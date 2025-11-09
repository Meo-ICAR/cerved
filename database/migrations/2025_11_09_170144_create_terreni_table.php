<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('terreni', function (Blueprint $table) {
            $table->id();
            $table->string('codice_immobile')->unique()->nullable();
            $table->foreignId('persona_id')->nullable()->constrained('people')->onDelete('cascade');
            $table->string('classe')->nullable();
            $table->string('codice_comune')->nullable();
            $table->string('codice_belfiore')->nullable();
            $table->string('descrizione_comune')->nullable();
            $table->string('codice_provincia', 2)->nullable();
            $table->string('foglio')->nullable();
            $table->string('particella')->nullable();
            $table->string('sezione_censuaria')->nullable();
            $table->string('codice_porzione')->nullable();
            $table->string('descrizione_qualita')->nullable();
            $table->decimal('superficie_ettari', 12, 2)->nullable();
            $table->decimal('superficie_are', 12, 2)->nullable();
            $table->decimal('superficie_centiare', 12, 2)->nullable();
            $table->decimal('rendita_dominicale', 12, 2)->nullable();
            $table->decimal('rendita_agraria', 12, 2)->nullable();
            $table->json('stima')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('codice_immobile');
            $table->index('codice_comune');
            $table->index('codice_belfiore');
            $table->index('codice_provincia');
            $table->index('persona_id');
        });

        // Add foreign key for provincia after table is created
        Schema::table('terreni', function (Blueprint $table) {
            $table->foreign('codice_provincia')
                  ->references('province_code')
                  ->on('provincie')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('terreni');
    }
};
