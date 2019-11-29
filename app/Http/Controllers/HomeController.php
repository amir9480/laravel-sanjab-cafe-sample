<?php

namespace App\Http\Controllers;

use App\Customer;
use Illuminate\Http\Request;
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
     * @return \Illuminate\Http\Response
     */
    public function getInfo(Request $request)
    {
        $request->validate([
            'mobile' => 'required_without:code|nullable|numeric',
            'code' => 'required_without:mobile|nullable|numeric',
        ], [
            'mobile.required_without' => 'حداقل یکی از فیلد های شماره همراه یا کد عضویت را تکمیل کنید.',
            'code.required_without' => 'حداقل یکی از فیلد های شماره همراه یا کد عضویت را تکمیل کنید.',
        ], [
            'mobile' => 'شماره همراه',
            'code' => 'کد عضویت',
        ]);
        $customer = Customer::query();
        if ($request->filled('code')) {
            $customer->where('id', $request->input('code'));
        } else {
            $customer->where('mobile', $request->input('mobile'));
        }
        if (!($customer = $customer->first())) {
            throw ValidationException::withMessages(['code' => 'کاربری با این '.($request->filled('code') ? 'کد عضویت' : 'شماره همراه').' یافت نشد.']);
        }
        return ['message' => 'سکه های شما: '.$customer->coin, 'id' => $customer->id];
    }
}
