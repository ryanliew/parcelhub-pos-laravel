<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCashupDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashup_details', function (Blueprint $table) {
            $table->increments('id');
            $table->float('expected_amount');
            $table->float('actual_amount')->nullable();
            $table->string('legend')->nullable();
            $table->string('type');
            $table->float('percentage');
            $table->integer('count')->nullable();
            $table->unsignedInteger('cashup_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cashup_details');
    }
}
