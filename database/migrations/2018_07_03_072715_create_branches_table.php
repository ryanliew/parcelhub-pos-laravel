<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('code');
            $table->string('owner');
            $table->string('contact');
            $table->string('email');
            $table->string('address');
            $table->string('registration_no');
            $table->string('gst_no')->nullable();
            $table->string('fax')->nullable();
            $table->string('tollfree')->nullable();
            $table->string('website')->nullable();
            $table->string('payment_bank');
            $table->string('payment_acc_no');
            $table->boolean('has_gst')->default(false);
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
        Schema::dropIfExists('branches');
    }
}
