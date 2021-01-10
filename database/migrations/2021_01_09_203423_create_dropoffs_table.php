<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDropoffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dropoffs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("branch_id");
            $table->unsignedInteger("terminal_id");
            $table->unsignedInteger("customer_id");
            $table->unsignedInteger("user_id");
            $table->string("consignment_note")->nullable();
            $table->string("status");
            $table->text("remarks")->nullable();
            $table->unsignedInteger("vendor_id")->nullable();
            $table->string("type");
            $table->dateTime("picked_up_on")->nullable();
            $table->string("dropoff_no");
            $table->string("picked_up_by")->nullable();
            $table->string("vehicle_no")->nullable();
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
        Schema::dropIfExists('dropoffs');
    }
}
