<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecordPaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string|in:cash,check,transfer,mobile',
            'reference' => 'nullable|string|max:100'
        ];
    }
}
