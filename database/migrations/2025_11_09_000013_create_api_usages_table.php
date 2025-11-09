<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('api_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('endpoint');
            $table->string('method', 10);
            $table->json('parameters')->nullable();
            $table->integer('response_code');
            $table->integer('response_time'); // in milliseconds
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('endpoint');
            $table->index('method');
            $table->index('response_code');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('api_usages');
    }
};
