<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Cart;
use App\Product;
use App\Customer;
use Faker\Generator as Faker;

$factory->define(Cart::class, function (Faker $faker) {
    return [
        'seen'        => $faker->boolean,
        'customer_id' => Customer::inRandomOrder()->first()->id,
        'date'        => $faker->dateTimeBetween('-10 days', '+10 days'),
    ];
});

$factory->afterCreating(Cart::class, function (Cart $cart, Faker $faker) {
    $products = Product::inRandomOrder()->limit($faker->numberBetween(1, 10))->get()->mapWithKeys(function ($product) use ($faker) {
        return [$product->id => ['quantity' => $faker->numberBetween(1, 3)]];
    });
    $cart->products()->sync($products->toArray());
});
