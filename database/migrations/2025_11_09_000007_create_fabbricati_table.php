<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fabbricati', function (Blueprint $table) {
            $table->id();
            $table->string('codice_immobile', 50)->unique();
            $table->string('tipo_immobile');
            $table->string('indirizzo');
            $table->string('civico', 20)->nullable();
            $table->string('cap', 5);
            $table->string('comune');
            $table->string('provincia', 2);
            $table->string('nazione', 3)->default('ITA');
            $table->decimal('superficie', 10, 2)->nullable();
            $table->integer('piani')->nullable();
            $table->integer('piano')->nullable();
            $table->string('stato_conservazione', 50)->nullable();
            $table->string('classe_energetica', 10)->nullable();
            $table->decimal('rendita_catastale', 12, 2)->nullable();
            $table->decimal('valore_commerciale', 12, 2)->nullable();
            $table->date('data_acquisto')->nullable();
            $table->decimal('prezzo_acquisto', 12, 2)->nullable();
            $table->string('catasto_particella', 20)->nullable();
            $table->string('catasto_foglio', 10)->nullable();
            $table->string('catasto_mappale', 10)->nullable();
            $table->string('catasto_subalterno', 10)->nullable();
            $table->string('catasto_ubicazione', 10)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('codice_immobile');
            $table->index('tipo_immobile');
            $table->index('comune');
            $table->index('provincia');
            $table->index('cap');
            
            // Foreign key to province
            $table->foreign('provincia')
                  ->references('sigla')
                  ->on('provincie')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fabbricati');
    }
};
