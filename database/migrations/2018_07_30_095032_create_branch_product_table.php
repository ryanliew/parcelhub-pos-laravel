<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBranchProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_product', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('branch_id');
            $table->unsignedInteger("product_id");
            $table->unsignedInteger("customer_id")->nullable();
            $table->boolean('is_tax_inclusive')->default(true);
            $table->float('corporate_price', 15, 2)->default(0.00);
            $table->float('walk_in_price', 15, 2)->default(0.00);
            $table->float('walk_in_price_special', 15, 2)->default(0.00);
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
        Schema::dropIfExists('branch_product');
    }
}
