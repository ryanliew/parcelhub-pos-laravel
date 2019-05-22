<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablesToHexapos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['tracking_code', 'zone', 'weight', 'dimension_weight', 'height', 'length', 'width', 'courier_id', 'zone_type_id']);
            $table->decimal('hours')->nullable();
            $table->decimal('vendor_name')->nullable();
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['zone_type_id', 'formula']);
        });

        Schema::table('product_types', function (Blueprint $table) {
            $table->dropColumn(['is_document', 'default_zone_type_id']);
            $table->boolean('is_hours')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['hours', 'vendor_name']);
            $table->unsignedInteger('zone_type_id')->nullable();
            $table->unsignedInteger('courier_id')->nullable();
            $table->string('zone')->nullable();
            $table->float('weight', 30, 3);
            $table->float('dimension_weight', 30, 3)->nullable();
            $table->float('height', 30, 3)->nullable();
            $table->float('length', 30, 3)->nullable();
            $table->float('width', 30, 3)->nullable();
            $table->string('tracking_code');
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->string('formula');
            $table->unsignedInteger('zone_type_id');
        });

        Schema::table('product_types', function (Blueprint $table) {
            $table->dropColumn(['is_hours']);
            $table->boolean('is_document')->default(false);
            $table->unsignedInteger('default_zone_type_id');
        });
    }
}
