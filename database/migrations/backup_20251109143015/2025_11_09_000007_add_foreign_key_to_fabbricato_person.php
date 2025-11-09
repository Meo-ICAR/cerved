<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddForeignKeyToFabbricatoPerson extends Migration
{
    public function up()
    {
        // First, check if the foreign key already exists
        $foreignKeys = DB::select("
            SELECT * FROM information_schema.TABLE_CONSTRAINTS 
            WHERE CONSTRAINT_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'fabbricato_person' 
            AND CONSTRAINT_NAME = 'fabbricato_person_person_id_foreign'
        ");

        if (empty($foreignKeys)) {
            // Add the foreign key constraint
            Schema::table('fabbricato_person', function (Blueprint $table) {
                $table->foreign('person_id')
                      ->references('id')
                      ->on('people')
                      ->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::table('fabbricato_person', function (Blueprint $table) {
            $table->dropForeign(['person_id']);
        });
    }
}
