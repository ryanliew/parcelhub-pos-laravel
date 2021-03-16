<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVendorNameToBilingAndBillingImport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billing_imports', function (Blueprint $table) {
            $table->string("vendor_name")->nullable();
        });

        Schema::table('billing_records', function (Blueprint $table) {
            $table->string("job_type")->nullable();
        });

        Schema::table('billings', function (Blueprint $table) {
            $table->string("vendor_name")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->dropColumn("vendor_name");
        });

        Schema::table('billing_records', function (Blueprint $table) {
            $table->string("job_type")->nullable();
        });

        Schema::table('billing_imports', function (Blueprint $table) {
            $table->dropColumn("vendor_name");
        });
    }
}
