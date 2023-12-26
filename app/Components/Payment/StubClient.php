<?php

namespace App\Components\Payment;

use App\Dto\CallbackDto;
use App\Http\Services\PaymentCallbackService;

class StubClient extends AbstractPaymentClient
{

    const WIDGET_VIEW = 'widget.stub';
    private string $event;
    private string $order_id;


    /**
     * @param array{order_id: int, price: int, return_url: string} $data
     * @return string
     */
    public function pay(array $data): string
    {
        $this->event = self::CALLBACK_EVENT_PAY;
        $this->order_id = $data['order_id'];
        app(PaymentCallbackService::class)->callback();
        return $data['return_url'];
    }

    /**
     * @param array{order_id: int, price: int, payout_token: string} $data
     * @return void
     */
    public function payout(array $data): void
    {
        $this->event = self::CALLBACK_EVENT_PAYOUT;
        $this->order_id = $data['order_id'];
        app(PaymentCallbackService::class)->callback();
    }

    /**
     * @param array{order_id: int, pay_id:string, price:int} $data
     * @return void
     */
    public function refund(array $data): void
    {
        $this->event = self::CALLBACK_EVENT_REFUND;
        $this->order_id = $data['order_id'];
        app(PaymentCallbackService::class)->callback();
    }

    public function getCallback(): CallbackDto
    {
        return new CallbackDto(
            id: uniqid('', true),
            event: $this->event ?? request()->input('event'),
            status: self::TRANSACTION_STATUS_SUCCEEDED,
            order_id: $this->order_id ?? request()->input('order_id'),
        );
    }

    public function authorizeCallback(): void
    {
    }

    public function getWidget(): string
    {
        return self::WIDGET_VIEW;
    }
}

