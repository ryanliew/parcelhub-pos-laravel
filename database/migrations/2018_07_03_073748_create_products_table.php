<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sku');
            $table->string('description');
            $table->integer('zone');
            $table->float('weight_start', 15, 3)->nullable();
            $table->float('weight_end', 15, 3)->nullable();
            $table->boolean('is_tax_inclusive')->default(true);
            $table->float('corporate_price', 15, 2)->default(0.00);
            $table->float('walk_in_price', 15, 2)->default(0.00);
            $table->float('walk_in_price_special', 15, 2)->default(0.00);
            $table->unsignedInteger('vendor_id')->nullable();
            $table->unsignedInteger('product_type_id');
            $table->unsignedInteger('tax_id');
            $table->unsignedInteger('zone_type_id')->nullable();
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
        Schema::dropIfExists('products');
    }
}
