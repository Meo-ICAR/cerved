<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('log_api_cerved', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            
            $table->string('endpoint_chiamato');
            $table->string('partita_iva_input', 11)->nullable();
            $table->integer('status_code_risposta');
            
            // Usiamo text invece di json per gestire anche risposte non JSON
            $table->text('request_payload')->nullable();
            $table->longText('response_payload')->nullable();
            
            $table->decimal('costo_chiamata', 10, 4)->nullable();
            
            $table->timestamps();
            
            // Indici per le ricerche comuni
            $table->index(['user_id', 'created_at']);
            $table->index(['endpoint_chiamato', 'status_code_risposta']);
            $table->index('partita_iva_input');
        });
    }

    public function down()
    {
        Schema::dropIfExists('log_api_cerved');
    }
};
