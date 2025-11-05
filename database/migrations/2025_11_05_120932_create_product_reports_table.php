<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_reports', function (Blueprint $table) {
            $table->id();
            $table->string('product'); // Per il campo "product"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_reports');
    }
};
