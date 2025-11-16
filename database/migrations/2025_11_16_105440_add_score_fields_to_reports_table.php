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
        Schema::table('reports', function (Blueprint $table) {
            $table->integer('id_soggetto');
            $table->char('codice_score', 20);
            $table->string('descrizione_score', 200);
            $table->integer('valore');
            $table->string('categoria_codice', 20);
            $table->string('categoria_descrizione', 200);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn([
                'id_soggetto',
                'codice_score',
                'descrizione_score',
                'valore',
                'categoria_codice',
                'categoria_descrizione'
            ]);
        });
    }
};
