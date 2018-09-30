<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCashupInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashup_invoice', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cashup_id');
            $table->unsignedInteger('invoice_id');
            $table->unsignedInteger('payment_id');
            $table->float('total', 15, 2);
            $table->string('payment_method');
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
        Schema::dropIfExists('cashup_invoice');
    }
}
