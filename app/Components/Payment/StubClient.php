<?php

namespace App\Components\Payment;

use App\Dto\Payment\PaymentCallbackDto;
use App\Dto\Payment\PaymentDto;

class StubClient extends AbstractPaymentClient
{

    const WIDGET_VIEW = 'widget.stub';
    private string $payment_id;
    private string $payment_type;
    private int $order_id;

    public function pay(PaymentDto $paymentDto): ?string
    {
        $this->pay_url = $paymentDto->return_url;
        return $this->payment($paymentDto);
    }

    public function payout(PaymentDto $paymentDto): ?string
    {
        return $this->payment($paymentDto);
    }

    public function refund(PaymentDto $paymentDto): ?string
    {
        return $this->payment($paymentDto);
    }

    public function payment(PaymentDto $paymentDto): string
    {
        $this->payment_type = $paymentDto->payment_type;
        $this->order_id = $paymentDto->order_id;
        return $this->payment_id = uniqid();
    }

    public function getCallback(): PaymentCallbackDto
    {
        return new PaymentCallbackDto(
            id: $this->payment_id,
            event: $this->payment_type,
            status: self::TRANSACTION_STATUS_SUCCEEDED,
            order_id: $this->order_id,
        );
    }

    protected function getWidgetName(): string
    {
        return self::WIDGET_VIEW;
    }

    public function authorizeCallback(): void
    {
    }
}

