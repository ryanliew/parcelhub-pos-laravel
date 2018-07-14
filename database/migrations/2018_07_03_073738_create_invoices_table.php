<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('remarks');
            $table->float('subtotal', 15, 2);
            $table->float('discount', 5, 2)->default(0.00);
            $table->float('discount_value', 30, 2)->default(0.00);
            $table->string('discount_mode')->default("%");
            $table->float('tax', 15, 2)->default(0.00);
            $table->float('total', 15, 2);
            $table->float('paid', 15, 2)->default(0.00);
            $table->unsignedInteger('branch_id');
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('customer_id')->nullable();
            $table->string('type')->default('cash');
            $table->string('payment_type');
            $table->integer('terminal_no');
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
        Schema::dropIfExists('invoices');
    }
}
