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

$factory->define(App\Branch::class, function (Faker $faker) {
	return [
		"name" => $faker->name,
		"code" => $tld,
		"owner" => $faker->name,
		"contact" => $faker->phoneNumber,
		"email" => $faker->unique()->safeEmail,
		"address" => $faker->address,
		"registration_no" => $faker->businessIdentificationNumber,
		"payment_bank" => $faker->company,
		"payment_acc_no" => $faker->businessIdentificationNumber,
		"terminal_count" => 1,
		"has_gst" => 0
	];
});