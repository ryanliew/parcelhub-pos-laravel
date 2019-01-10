<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActualAmountToCashup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cashups', function (Blueprint $table) {
            $table->float('actual_amount')->default(0);
            $table->string('status')->default('draft');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cashups', function (Blueprint $table) {
            $table->dropColumn(['actual_amount', 'draft']);
        });
    }
}
