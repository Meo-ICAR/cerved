<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('comuni', function (Blueprint $table) {
            // Primary key
            $table->id();
            
            // Comune data
            $table->string('belfiore_code', 4)->unique();
            $table->string('istat_code_municipality', 10)->unique();
            $table->string('istat_code_province', 10);
            $table->string('municipality_code', 10);
            $table->string('municipality_description', 255);
            $table->string('province_code', 2);
            $table->string('zip_code', 5);
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index('province_code');
            $table->index('municipality_description');
            $table->index('zip_code');
            
            // Foreign key to provincie table
            $table->foreign('province_code')
                  ->references('province_code')
                  ->on('provincie')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('comuni');
    }
};
