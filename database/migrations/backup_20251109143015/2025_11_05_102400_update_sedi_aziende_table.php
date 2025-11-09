<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sedi_aziende', function (Blueprint $table) {
            // Rimuovi le colonne non più necessarie
            $table->dropColumn([
                'regione',
                'nazione',
                'telefono',
                'email',
                'pec',
                'is_legale',
                'is_operativa'
            ]);
        });
    }

    public function down()
    {
        Schema::table('sedi_aziende', function (Blueprint $table) {
            // Ripristina le colonne rimosse
            $table->string('regione')->nullable()->after('provincia');
            $table->string('nazione')->default('Italia')->after('regione');
            $table->string('telefono')->nullable()->after('nazione');
            $table->string('email')->nullable()->after('telefono');
            $table->string('pec')->nullable()->after('email');
            $table->boolean('is_legale')->default(false)->after('pec');
            $table->boolean('is_operativa')->default(true)->after('is_legale');
        });
    }
};
