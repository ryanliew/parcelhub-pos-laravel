<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSessionsAddInvoiceInformation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->float('discount', 5, 2)->default(0.00)->nullable();
            $table->float('discount_value', 30, 2)->default(0.00)->nullable();
            $table->string('discount_mode')->default("%")->nullable();
            $table->float('paid', 15, 2)->default(0.00)->nullable();
            $table->string('payment_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropColumn(['discount', 'discount_value', 'discount_mode', 'paid', 'payment_type']);
        });
    }
}
