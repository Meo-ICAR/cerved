<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bilanci', function (Blueprint $table) {
            // Rimuovi le colonne non più necessarie
            $table->dropColumn([
                'data_chiusura',
                'esercizio_chiuso',
                'valuta',
                'patrimonio_netto',
                'attivo_circolante',
                'totale_attivo',
                'totale_passivo',
                'dati_completi'
            ]);

            // Aggiungi le nuove colonne
            $table->decimal('ebitda', 15, 2)->nullable()->after('fatturato');
            $table->decimal('utile_netto', 15, 2)->nullable()->after('ebitda');
            $table->integer('numero_dipendenti')->nullable()->after('utile_netto');
            $table->json('bilancio_completo')->nullable()->after('numero_dipendenti');
        });
    }

    public function down()
    {
        Schema::table('bilanci', function (Blueprint $table) {
            // Ripristina le colonne rimosse
            $table->date('data_chiusura')->nullable();
            $table->boolean('esercizio_chiuso')->default(true);
            $table->string('valuta', 3)->default('EUR');
            $table->decimal('patrimonio_netto', 15, 2)->nullable();
            $table->decimal('attivo_circolante', 15, 2)->nullable();
            $table->decimal('totale_attivo', 15, 2)->nullable();
            $table->decimal('totale_passivo', 15, 2)->nullable();
            $table->json('dati_completi')->nullable();

            // Rimuovi le colonne aggiunte
            $table->dropColumn([
                'ebitda',
                'utile_netto',
                'numero_dipendenti',
                'bilancio_completo'
            ]);
        });
    }
};
