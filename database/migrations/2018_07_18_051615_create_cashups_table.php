<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCashupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashups', function (Blueprint $table) {
            $table->increments('id');
            $table->float('total', 15, 2);
            $table->string('invoice_from');
            $table->string('invoice_to');
            $table->unsignedInteger('branch_id');
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('terminal_id');
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
        Schema::dropIfExists('cashups');
    }
}
