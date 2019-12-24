<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyToHeadStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->renameColumn('table_id', 'head_id');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->renameColumn('invoice_id', 'session_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->renameColumn('head_id', 'table_id');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->renameColumn('session_id', 'invoice_id');
        });
    }
}
