<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fabbricato_persona', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fabbricato_id')->constrained('fabbricati')->onDelete('cascade');
            $table->foreignId('persona_id')->constrained('people')->onDelete('cascade');
            $table->string('tipo_relazione'); // Proprietario, Comproprietario, Usufruttuario, ecc.
            $table->decimal('quota_possesso', 5, 2)->nullable(); // Percentuale di possesso
            $table->date('data_inizio')->nullable();
            $table->date('data_fine')->nullable();
            $table->json('dati_aggiuntivi')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('fabbricato_id');
            $table->index('persona_id');
            $table->index('tipo_relazione');
            
            // Unique constraint
            $table->unique(['fabbricato_id', 'persona_id', 'tipo_relazione', 'data_inizio'], 'unique_fabbricato_persona');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fabbricato_persona');
    }
};
