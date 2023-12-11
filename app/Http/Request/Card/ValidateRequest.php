<?php

namespace App\Http\Request\Card;

use Illuminate\Foundation\Http\FormRequest;

class ValidateRequest extends FormRequest
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

    public function prepareForValidation(): void
    {
        $this->merge([
            'card' => json_decode($this->input('data'), true)
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<mixed>
     */
    public function rules(): array
    {
        return [
            'data' => 'string|required',
            'card.payout_token' => 'string|required',
            'card.first6' => 'required|digits:6',
            'card.last4' => 'required|digits:4',
            'card.card_type' => 'string|required',
            'card.issuer_country' => 'string|required',
        ];
    }
}
