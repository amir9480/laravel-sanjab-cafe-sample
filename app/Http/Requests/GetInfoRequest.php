<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetInfoRequest extends FormRequest
{
    public function rules()
    {
        return [
            'mobile' => 'required_without:code|nullable|numeric',
            'code'   => 'required_without:mobile|nullable|numeric',
        ];
    }

    public function messages()
    {
        return [
            'mobile.required_without' => 'حداقل یکی از فیلد های شماره همراه یا کد عضویت را تکمیل کنید.',
            'code.required_without'   => 'حداقل یکی از فیلد های شماره همراه یا کد عضویت را تکمیل کنید.',
        ];
    }

    public function attributes()
    {
        return [
            'mobile' => 'شماره همراه',
            'code'   => 'کد عضویت',
        ];
    }
}
