<?php

namespace App\Http\Services;

use App\Components\Payment\PaymentClientInterface;

class PaymentCallbackService
{

    public function __construct(private readonly PaymentClientInterface $paymentClient)
    {
    }

    public function callback(): void
    {
        $this->paymentClient->authorizeCallback();
        $callbackDto = $this->paymentClient->getCallback();
        $this->paymentClient->sendCallbackNotification($callbackDto);
    }
}
