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
        Schema::create('api_usages', function (Blueprint $table) {
            $table->id();
            $table->string('product');
            $table->json('statistics');
            $table->date('report_date')->index();
            $table->timestamps();
            
            // Indice per ricerche per prodotto e data
            $table->index(['product', 'report_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_usages');
    }
};
