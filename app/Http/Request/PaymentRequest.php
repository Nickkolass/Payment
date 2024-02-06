<?php

namespace App\Http\Request;

use App\Components\Payment\AbstractPaymentClient;
use App\Components\Payment\PaymentClientInterface;
use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
        return self::getRules();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     */
    public static function getRules(): array
    {
        return [
            'payment_type' => 'required|string|in:' . implode(',', AbstractPaymentClient::getPaymentTypes()),
            'order_id' => 'required|int',
            'price' => 'required|int',
            'return_url' => 'nullable|url|prohibits:payout_token,pay_id|required_if:event,' . PaymentClientInterface::CALLBACK_PAYMENT_TYPE_PAY,
            'payout_token' => 'nullable|string|prohibits:return_url,pay_id|required_if:event,' . PaymentClientInterface::CALLBACK_PAYMENT_TYPE_PAYOUT,
            'pay_id' => 'nullable|string|prohibits:payout_token,return_url|required_if:event,' . PaymentClientInterface::CALLBACK_PAYMENT_TYPE_REFUND,
        ];
    }
}
