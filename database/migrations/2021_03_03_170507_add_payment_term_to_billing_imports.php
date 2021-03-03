<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentTermToBillingImports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billing_imports', function (Blueprint $table) {
            $table->integer("payment_term")->nullable();
        });

        Schema::table('billings', function (Blueprint $table) {
            $table->integer("payment_term")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('billing_imports', function (Blueprint $table) {
            $table->dropColumn("payment_term");
        });

        Schema::table('billings', function (Blueprint $table) {
            $table->dropColumn("payment_term")->nullable();
        });
    }
}
