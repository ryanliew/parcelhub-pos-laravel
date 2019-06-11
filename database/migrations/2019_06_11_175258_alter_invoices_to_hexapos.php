<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterInvoicesToHexapos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['discount', 'discount_value', 'discount_mode', 'paid', 'payment_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->float('discount', 5, 2)->default(0.00);
            $table->float('discount_value', 30, 2)->default(0.00);
            $table->string('discount_mode')->default("%");
            $table->float('paid', 15, 2)->default(0.00);
            $table->string('payment_type');
        });
    }
}
