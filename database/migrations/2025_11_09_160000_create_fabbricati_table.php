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
        Schema::create('fabbricati', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('persona_id')->nullable();
            $table->string('codice_immobile', 50)->unique();
            $table->string('classe', 10)->nullable();
            $table->string('codice_comune', 10)->nullable();
            $table->string('codice_belfiore', 10)->nullable();
            $table->string('codice_categoria', 255)->nullable();
            $table->string('descrizione_categoria', 255)->nullable();
            $table->string('indirizzo', 255)->nullable();
            $table->string('cap', 5)->nullable();
            $table->string('descrizione_comune', 255)->nullable();
            $table->string('codice_provincia', 2)->nullable();
            $table->string('foglio', 20)->nullable();
            $table->string('particella', 20)->nullable();
            $table->string('unita_misura_consistenza', 20)->nullable();
            $table->decimal('valore_consistenza', 12, 2)->nullable();
            $table->decimal('superficie', 10, 2)->nullable();
            $table->integer('piano')->nullable();
            $table->decimal('rendita', 12, 2)->nullable();
            $table->json('stima')->nullable();
            $table->string('particella_temp', 20)->nullable();
            $table->string('foglio_temp', 10)->nullable();
            $table->string('subalterno', 10)->nullable();
            $table->timestamps();

            // Add indexes
            $table->index('codice_immobile');
            $table->index('codice_comune');
            $table->index('codice_belfiore');
            $table->index('codice_provincia');
            $table->index('persona_id');

            // Add foreign key constraints
            $table->foreign('codice_provincia')
                  ->references('province_code')
                  ->on('provincie')
                  ->onUpdate('cascade');
                  
            $table->foreign('persona_id')
                  ->references('id')
                  ->on('people')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fabbricati');
    }
};
