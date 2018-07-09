<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterItemsNullableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->unsignedInteger("courier_id")->nullable()->change();
            $table->string("zone")->nullable()->change();
            $table->float("dimension_weight", 30, 3)->nullable()->change();
            $table->float("height", 30, 3)->nullable()->change();
            $table->float("width", 30, 3)->nullable()->change();
            $table->float("length", 30, 3)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->unsignedInteger("courier_id")->change();
            $table->string("zone")->change();
            $table->float("dimension_weight", 30, 3)->change();
            $table->float("height", 30, 3)->change();
            $table->float("width", 30, 3)->change();
            $table->float("length", 30, 3)->change();
        });
    }
}
