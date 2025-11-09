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
        Schema::create('comuni', function (Blueprint $table) {
            $table->id();
            $table->string('belfiore_code', 4)->unique();
            $table->string('istat_code_municipality', 10)->unique();
            $table->string('istat_code_province', 10);
            $table->string('municipality_code', 10);
            $table->string('municipality_description');
            $table->string('province_code', 2);
            $table->string('zip_code', 5);
            $table->timestamps();
            
            // Aggiungi indici per le ricerche comuni
            $table->index('province_code');
            $table->index('municipality_description');
            $table->index('zip_code');
            
            // Chiave esterna verso la tabella delle province
            $table->foreign('province_code')
                  ->references('province_code')
                  ->on('provincie')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comuni');
    }
};
