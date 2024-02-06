<?php

namespace App\Components\Payment;

use App\Dto\CallbackDto;

interface PaymentClientInterface
{

    /**
     * @param array{order_id: int, price: int, return_url: string} $data
     * @return string
     */
    public function pay(array $data): string;

    /**
     * @param array{order_id: int, price: int, payout_token: string} $data
     * @return void
     */
    public function payout(array $data): void;

    /**
     * @param array{order_id: int, pay_id:string, price:int} $data
     * @return void
     */
    public function refund(array $data): void;

    public function authorizeCallback(): void;

    public function getCallback(): CallbackDto;

    public function sendCallbackNotification(CallbackDto $callbackDto): void;

    public function getWidget(): string;
}
