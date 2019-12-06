<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Customer;
use App\Transaction;
use Faker\Generator as Faker;

$factory->define(Customer::class, function (Faker $faker) {
    return [
        'mobile'    => $faker->iranMobile,
        'coin'      => $faker->numberBetween(0, 20),
        'total_buy' => $faker->numberBetween(0, 1000) * 1000,
    ];
});

$factory->afterCreating(Customer::class, function (Customer $customer, Faker $faker) {
    factory(Transaction::class, $faker->numberBetween(0, 40))->create(['customer_id' => $customer->id]);
});
