<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_records', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("billing_imports_id");
            $table->string("sub_account")->nullable();
            $table->string("invoice_no_ext")->nullable();
            $table->string("hawb")->nullable();
            $table->string("pickup_date")->nullable();
            $table->string("ref")->nullable();
            $table->string("shipper_origin")->nullable();
            $table->string("special_zone")->nullable();
            $table->string("destination")->nullable();
            $table->string("prod_type")->nullable();
            $table->string("weight")->nullable();
            $table->string("pcs")->nullable();
            $table->string("shipping_charge")->nullable();
            $table->string("fsc")->nullable();
            $table->string("net_charges")->nullable();
            $table->string("sst_rate")->nullable();
            $table->string("amount")->nullable();
            $table->string("lc_marking")->nullable();
            $table->string("invoice_no_self")->nullable();
            $table->string("base_amount")->nullable();
            $table->string("surcharge")->nullable();
            $table->string("total_bill_amount")->nullable();
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
        Schema::dropIfExists('billing_records');
    }
}
