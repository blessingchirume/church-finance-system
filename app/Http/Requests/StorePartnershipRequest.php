<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePartnershipRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'member_id' => 'required|exists:members,id|unique:partners,member_id',
            'amount' => 'required|numeric|min:1'
        ];
    }
}
