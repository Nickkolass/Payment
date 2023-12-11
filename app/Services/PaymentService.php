<?php

namespace App\Services;

use App\Components\Payment\PaymentClientInterface;

class PaymentService
{
    public function __construct(public readonly PaymentClientInterface $paymentClient)
    {
    }

    /**
     * @param array{order_id: int, price: int, return_url: string} $data
     * @return string
     */
    public function pay(array $data): string
    {
        return $this->paymentClient->pay($data);
    }

    /**
     * @param array{order_id: int, price: int, payout_token: string} $data
     * @return void
     */
    public function payout(array $data): void
    {
        $this->paymentClient->payout($data);
    }

    /**
     * @param array{order_id: int, pay_id:string, price:int} $data
     * @return void
     */
    public function refund(array $data): void
    {
        $this->paymentClient->refund($data);
    }
}
