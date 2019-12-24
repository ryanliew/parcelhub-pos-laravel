<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterInvoicesToHaveMultipleSessions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn("session_id");
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->unsignedInteger("invoice_id")->nullable();
            $table->unsignedInteger("member_id")->nullable();
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
            $table->unsignedInteger("session_id");
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->dropColumn("invoice_id", "member_id");
        });
    }
}
