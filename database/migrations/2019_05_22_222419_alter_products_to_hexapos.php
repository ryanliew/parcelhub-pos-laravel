<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProductsToHexapos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['zone', 'weight_start', 'weight_end', 'corporate_price', 'walk_in_price', 'walk_in_price_special', 'zone_type_id']);
            $table->decimal('hour_start')->nullable();
            $table->decimal('hour_end')->nullable();
            $table->decimal('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['hour_start', 'hour_end', 'price']);
            $table->integer('zone');
            $table->float('weight_start', 15, 3)->nullable();
            $table->float('weight_end', 15, 3)->nullable();
            $table->float('corporate_price', 15, 2)->default(0.00);
            $table->float('walk_in_price', 15, 2)->default(0.00);
            $table->float('walk_in_price_special', 15, 2)->default(0.00);
        });
    }
}
