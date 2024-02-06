<?php

namespace App\Dto\Payment;

class PaymentCallbackDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $event,
        public readonly string $status,
        public readonly int    $order_id,
    )
    {
    }
}
