<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Item::class, function (Faker $faker) {
	return [
		"tracking_code" => $faker->businessIdentificationNumber,
		"description" => $faker->sentence,
		"zone" => 2,
		"weight" => 2,
		"dimension_weight" => 0,
		"height" => 0,
		"length" => 0,
		"width" => 0,
		"sku" => $faker->word,
		"tax" => 0.00,
		"price" => 100.00,
		"courier_id" => 1,
		"invoice_id" => 1,
		"product_id" => 1,
		"product_type_id" => 1,
		"total_price" => 100.00,
		"unit" => 1
	];
});