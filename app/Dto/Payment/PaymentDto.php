<?php

namespace App\Dto\Payment;

use App\Http\Request\PaymentRequest;
use Illuminate\Support\Facades\Validator;

class PaymentDto
{
    public function __construct(
        public readonly string  $payment_type,
        public readonly int     $order_id,
        public readonly int     $price,
        public readonly ?string $pay_id = null,
        public readonly ?string $payout_token = null,
        public readonly ?string $return_url = null,
    )
    {
        Validator::validate((array)$this, PaymentRequest::getRules());
    }
}
