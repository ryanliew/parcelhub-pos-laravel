<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tracking_code');
            $table->string('description');
            $table->string('zone');
            $table->float('weight', 30, 3);
            $table->float('dimension_weight', 30, 3);
            $table->float('height', 30, 3);
            $table->float('length', 30, 3);
            $table->float('width', 30, 3);
            $table->string('sku');
            $table->float('tax', 15, 2);
            $table->float('price', 15, 2);
            $table->unsignedInteger('courier_id');
            $table->unsignedInteger('invoice_id');
            $table->unsignedInteger('product_type_id');
            $table->float('total_price');
            $table->integer('unit');
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
        Schema::dropIfExists('items');
    }
}
