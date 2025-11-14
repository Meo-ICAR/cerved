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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('piva', 20);
            $table->boolean('israces')->default(true);
            $table->longText('annotation')->nullable();
            $table->integer('apicervedcode')->nullable();
            $table->json('apicervedresponse')->nullable();
            $table->dateTime('apiactivation')->nullable();
            $table->json('mediaresponse')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
