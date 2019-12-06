<?php

namespace App\Http\Requests;

use App\Customer;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Foundation\Http\FormRequest;

class BuyRequest extends FormRequest
{
    public function rules()
    {
        $customer = Customer::find($this->input('id'));
        $recentCart = null;
        if ($customer) {
            $recentCart = $customer->carts()->recent()->first();
        }
        return [
            'id'                => 'required|numeric|exists:customers,id',
            'cart'              => 'required|array',
            'time'              => ($recentCart ? 'nullable':'required').'|numeric|min:30|max:720',
            'cart.*.product_id' => 'required|numeric|distinct|exists:products,id',
            'cart.*.quantity'   => 'required|numeric|min:1|max:100',
        ];
    }

    public function attributes()
    {
        return [
            'id'                => 'شناسه',
            'cart'              => 'سبد خرید',
            'cart.*.product_id' => 'محصول',
            'cart.*.quantity'   => 'تعداد محصول',
            'time'              => 'زمان مراجعه',
        ];
    }

    public function getValidatorInstance()
    {
        $allInputs = $this->all();
        if ($allInputs['id']) {
            $allInputs['id'] = Crypt::decrypt($allInputs['id']);
        }
        $this->replace($allInputs);
        return parent::getValidatorInstance();
    }
}
