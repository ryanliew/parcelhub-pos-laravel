<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBillingDatesToImport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billing_imports', function (Blueprint $table) {
            $table->date("invoice_date")->nullable();
            $table->date("billing_start")->nullable();
            $table->date("billing_end")->nullable();
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
            $table->dropColumn("invoice_date", "billing_start", "billing_end");
        });
    }
}
