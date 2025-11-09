<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_statistics', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to link to the parent report
            $table->foreignId('product_report_id')
                  ->constrained('product_reports')
                  ->cascadeOnDelete();

            // Fields from your API
            $table->string('developer');
            $table->string('app');
            $table->string('status');
            $table->integer('count');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_statistics');
    }
};
