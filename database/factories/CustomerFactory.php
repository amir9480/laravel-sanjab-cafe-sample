<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Customer;
use Faker\Generator as Faker;

$factory->define(Customer::class, function (Faker $faker) {
    return [
        'mobile' => $faker->iranMobile,
        'coin' => $faker->numberBetween(0, 20),
        'point' => $faker->numberBetween(0, 1000),
    ];
});
