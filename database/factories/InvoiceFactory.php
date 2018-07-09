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

$factory->define(App\Invoice::class, function (Faker $faker) {
    return [
        'remarks' => $faker->sentence,
        'subtotal' => 100.00,
        'total' => 100.00,
        'paid' => 100.00,
        'branch_id' => function() {
            return factory("App\Branch")->create()->id;
        },
        'created_by' => function() {
            return factory("App\User")->create()->id;
        },
        "payment_type" => 'cash',
        "terminal_no" => 1
    ];
});
