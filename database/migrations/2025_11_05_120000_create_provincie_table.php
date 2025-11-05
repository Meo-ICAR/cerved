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
        Schema::create('provincie', function (Blueprint $table) {
            $table->id();
            $table->boolean('activity')->default(true);
            $table->string('flag_active', 10)->default('Y');
            $table->string('region_code', 10)->index();
            $table->string('istat_code_province', 10)->unique();
            $table->string('province_code', 2)->unique();
            $table->string('province_description');
            $table->timestamps();
            
            // Aggiungi un indice composto per le ricerche comuni
            $table->index(['region_code', 'province_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provincie');
    }
};
