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
            // Primary key
            $table->id();
            
            // Province data
            $table->boolean('activity')->default(true);
            $table->string('flag_active', 10)->default('Y');
            $table->string('region_code', 10);
            $table->string('istat_code_province', 10);
            $table->string('province_code', 2);
            $table->string('province_description', 255);
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->unique('istat_code_province', 'provincie_istat_code_province_unique');
            $table->unique('province_code', 'provincie_province_code_unique');
            $table->index(['region_code', 'province_code'], 'provincie_region_code_province_code_index');
            $table->index('region_code', 'provincie_region_code_index');
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
