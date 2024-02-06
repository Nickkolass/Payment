<?php

namespace App\Http\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CardValidateRequest extends FormRequest
{


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     */
    public function rules(): array
    {
        return [
            'payout_token' => 'required|string',
            'first6' => 'required|digits:6',
            'last4' => 'required|digits:4',
            'card_type' => 'required|string',
            'issuer_country' => 'required|string',
        ];
    }

    /**
     * Get the "after" validation callables for the request.
     *
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $errors = $validator->errors()->all();
                if (!empty($errors)) response($errors, 400)->send();
            }
        ];
    }
}
