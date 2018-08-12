<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsPaymentInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_invoices', function($table) {
            $table->float('invoice_total', 15, 2);
            $table->float('outstanding', 15, 2);
            $table->float('paid', 15, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_invoices', function($table) {
            $table->dropColumn('total');
            $table->dropColumn('outstanding');
            $table->dropColumn('paid');
        });
    }
}
