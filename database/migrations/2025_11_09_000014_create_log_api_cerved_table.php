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
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('endpoint');
            $table->string('method', 10);
            $table->json('request_headers')->nullable();
            $table->json('request_body')->nullable();
            $table->integer('status_code');
            $table->json('response_headers')->nullable();
            $table->longText('response_body')->nullable();
            $table->integer('execution_time')->comment('in milliseconds');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('endpoint');
            $table->index('method');
            $table->index('status_code');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('log_api_cerved');
    }
};
