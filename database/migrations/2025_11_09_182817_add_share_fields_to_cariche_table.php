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
        Schema::table('cariche', function (Blueprint $table) {
            $table->integer('numero_quote')->nullable()->after('data_fine_carica');
            $table->decimal('valore_totale_quote', 15, 2)->nullable()->after('numero_quote');
            $table->decimal('quota_massima_societa', 5, 2)->nullable()->after('valore_totale_quote');
            $table->decimal('percentuale_quota_partecipazione', 5, 2)->nullable()->after('quota_massima_societa');
            $table->string('tipo_diritto', 1)->nullable()->after('percentuale_quota_partecipazione');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cariche', function (Blueprint $table) {
            $table->dropColumn([
                'numero_quote',
                'valore_totale_quote',
                'quota_massima_societa',
                'percentuale_quota_partecipazione',
                'tipo_diritto'
            ]);
        });
    }
};
