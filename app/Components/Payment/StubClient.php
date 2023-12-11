<?php

namespace App\Components\Payment;

use App\Dto\CallbackDto;
use Illuminate\Support\Facades\Http;

class StubClient extends AbstractPaymentClient
{

    const WIDGET_VIEW = 'payment::widget.stub';


    /**
     * @param array{order_id: int, price: int, return_url: string} $data
     * @return string
     */
    public function pay(array $data): string
    {
        if (request()->route()->getName() == 'back.api.orders.pay') {
            $requestBody = [
                'event' => self::CALLBACK_EVENT_PAY,
                'order_id' => $data['order_id'],
            ];
            $callbackDto = $this->getCallback($requestBody);
            Http::post(route('back.api.callback.pay'), (array)$callbackDto);
        }
        return route('client.orders.index', '', false);
    }

    /**
     * @param array{order_id: int, price: int, payout_token: string} $data
     * @return void
     */
    public function payout(array $data): void
    {
        if (request()->route()->getName() == 'admin.payout') {
            $requestBody = [
                'event' => self::CALLBACK_EVENT_PAYOUT,
                'order_id' => $data['order_id'],
            ];
            $callbackDto = $this->getCallback($requestBody);
            Http::post(route('admin.callback.payout'), (array)$callbackDto);
        }
    }

    /**
     * @param array{order_id: int, pay_id:string, price:int} $data
     * @return void
     */
    public function refund(array $data): void
    {
        if (request()->route()->getName() == 'back.api.orders.refund') {
            $requestBody = [
                'event' => self::CALLBACK_EVENT_REFUND,
                'order_id' => $data['order_id'],
            ];
            $callbackDto = $this->getCallback($requestBody);
            Http::post(route('back.api.callback.refund'), (array)$callbackDto);
        }
    }

    public function getCallback(mixed $requestBody): CallbackDto
    {
        return new CallbackDto(
            id: uniqid('', true),
            event: $requestBody['event'],
            status: self::TRANSACTION_STATUS_SUCCEEDED,
            order_id: $requestBody['order_id'],
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

