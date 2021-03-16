<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeBillingItemsNullableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billing_items', function (Blueprint $table) {
            $table->string("posting_date")->nullable()->change();
            $table->string("consignment_no")->nullable()->change();
            $table->string("weight")->nullable()->change();
            $table->string("zone")->nullable()->change();
            $table->string("charges")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('billing_items', function (Blueprint $table) {
            //
        });
    }
}
