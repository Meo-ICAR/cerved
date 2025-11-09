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
        Schema::create('possessi', function (Blueprint $table) {
            $table->id();
            $table->string('descrizione_titolo');
            $table->string('titolarita_orig');
            $table->string('quota_orig');
            $table->decimal('percentuale_quota', 5, 2);
            $table->unsignedBigInteger('possessibile_id');
            $table->string('possessibile_type');
            $table->timestamps();
            
            // Add index for polymorphic relationship
            $table->index(['possessibile_id', 'possessibile_type']);
            
            // Add indexes for searchable fields
            $table->index('descrizione_titolo');
            $table->index('quota_orig');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('possessi');
    }
};
