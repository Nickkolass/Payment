<?php

namespace App\Services;

use App\Components\Payment\AbstractPaymentClient;
use App\Components\Payment\PaymentClientInterface;
use Illuminate\Support\Facades\Http;

class PaymentCallbackService
{

    public function __construct(public readonly PaymentClientInterface $paymentClient)
    {
        $this->paymentClient->authorizeCallback();
    }

    public function callback(): void
    {
        $source = file_get_contents('php://input');
        $requestBody = json_decode((string)$source, true);
        $callbackDto = $this->paymentClient->getCallback($requestBody);

        if ($callbackDto->status == AbstractPaymentClient::TRANSACTION_STATUS_SUCCEEDED) {
            Http::async()->post(route('back.api.payment.callback'), (array)$callbackDto);
        } else \Log::info((string)json_encode($callbackDto));
    }
}
