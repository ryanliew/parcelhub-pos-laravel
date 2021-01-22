<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string("posting_date");
            $table->string("pl_9")->nullable();
            $table->string("subaccount")->nullable();
            $table->string("consignment_no");
            $table->string("weight");
            $table->string("zone");
            $table->string("charges");
            $table->unsignedInteger("billing_id");
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
        Schema::dropIfExists('billing_items');
    }
}
