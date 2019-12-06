<?php

namespace App\Http\Controllers;

use App\Category;
use App\Customer;
use Illuminate\Http\Request;
use App\Http\Requests\BuyRequest;
use App\Http\Requests\GetInfoRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;

class HomeController extends Controller
{
    /**
     * Show home page
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return view('home');
    }

    /**
     * Get information for home page
     *
     * @param GetInfoRequest $request
     * @return array
     */
    public function getInfo(GetInfoRequest $request)
    {
        $customer = Customer::query();
        if ($request->filled('code') && !$request->filled('mobile')) {
            $customer->where('id', $request->input('code'));
        } elseif ($request->filled('mobile') && !$request->filled('code')) {
            $customer->where('mobile', $request->input('mobile'));
        } else {
            $customer->where('mobile', $request->input('mobile'))->orWhere('id', $request->input('code'));
        }
        if (!($customer = $customer->first())) {
            throw ValidationException::withMessages(['code' => 'کاربری با این '.($request->filled('code') ? 'کد عضویت' : 'شماره همراه').' یافت نشد.']);
        }
        if ($request->filled('mobile') && $request->filled('code')) {
            if ($customer->mobile != $request->input('mobile') || $customer->id != $request->input('code')) {
                throw ValidationException::withMessages(['code' => 'این شماره همراه مطعلق به این کاربر نمیباشد']);
            }
            $recentCart = $customer->carts()->recent()->first();
            if ($recentCart) {
                $recentCart->dateForHuman = $recentCart->date->diffForHumans();
            }
            return [
                'id'         => Crypt::encrypt($customer->id),
                'coins'      => $customer->coin,
                'categories' => Category::with('products')->get(),
                'recentCart' => $recentCart,
            ];
        }
        return ['message' => 'سکه های شما: '.$customer->coin."\nجهت ثبت سفارش شماره همراه و کدعضویت را با هم وارد کنید."];
    }

    /**
     * Buy.
     *
     * @param BuyRequest $request
     * @return array
     */
    public function buy(BuyRequest $request)
    {
        $customer = Customer::findOrFail($request->input('id'));
        $cart = $customer->carts()->recent()->first();
        if ($cart == null) {
            $cart = $customer->carts()->create(['date' => now()->addMinutes($request->input('time'))]);
        }
        $products = collect($request->input('cart'))->mapWithKeys(function ($cartItem) {
            return [$cartItem['product_id'] => ['quantity' => $cartItem['quantity']]];
        })->toArray();
        $cart->products()->sync($products);
        return ['success' => true];
    }
}
