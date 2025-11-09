<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFabbricatoPersonaTable extends Migration
{
    public function up()
    {
        Schema::create('fabbricato_persona', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('fabbricato_id');
            $table->unsignedBigInteger('persona_id');
            $table->string('tipo_relazione', 100);
            $table->decimal('quota', 5, 2)->nullable();
            $table->date('data_inizio')->nullable();
            $table->date('data_fine')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('fabbricato_id');
            $table->index('persona_id');

            // Foreign key constraints
            $table->foreign('fabbricato_id')
                  ->references('id_immobile')
                  ->on('fabbricati')
                  ->onDelete('cascade');
                  
            $table->foreign('persona_id')
                  ->references('id')
                  ->on('persone')
                  ->onDelete('cascade');
            
            // Ensure we don't have duplicate relationships
            $table->unique(['fabbricato_id', 'persona_id', 'tipo_relazione'], 'idx_unique_fabbricato_persona_rel');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fabbricato_persona');
    }
}
