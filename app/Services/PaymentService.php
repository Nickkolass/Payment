<?php

namespace App\Services;

use App\Components\Payment\PaymentClientInterface;
use App\Dto\Payment\PaymentCallbackDto;
use App\Dto\Payment\PaymentDto;
use Log;

class PaymentService
{

    public function __construct(private readonly PaymentClientInterface $paymentClient)
    {
    }

    public function getRenderedWidget(string $_token, string $return_url): string
    {
        return $this->paymentClient->getWidget($_token, $return_url);
    }

    public function pay(PaymentDto $paymentDto): ?string
    {
        $pay_id = $this->paymentClient->pay($paymentDto);
        /** @phpstan-ignore-next-line */
        response($this->paymentClient->pay_url, 200)->send();
        return $pay_id;

    }

    public function payout(PaymentDto $paymentDto): ?string
    {
        return $this->paymentClient->payout($paymentDto);
    }

    public function refund(PaymentDto $paymentDto): ?string
    {
        return $this->paymentClient->refund($paymentDto);
    }

    public function callback(): ?PaymentCallbackDto
    {
        $this->paymentClient->authorizeCallback();
        $callbackDto = $this->paymentClient->getCallback();
        if ($callbackDto->status != $this->paymentClient::TRANSACTION_STATUS_SUCCEEDED) {
            Log::info((string)json_encode($callbackDto));
            return null;
        }
        return $callbackDto;
    }
}
