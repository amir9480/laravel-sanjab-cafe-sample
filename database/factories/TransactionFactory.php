<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Customer;
use App\Transaction;
use Faker\Generator as Faker;

$factory->define(Transaction::class, function (Faker $faker) {
    $byCoin = $faker->numberBetween(0, 10) == 0;
    return [
        'money' => $byCoin ? 0 : $faker->numberBetween(1, 50) * 1000,
        'coin' => $byCoin ? $faker->numberBetween(0, 50) : 0,
    ];
});
