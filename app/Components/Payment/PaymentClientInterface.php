<?php

namespace App\Components\Payment;

use App\Dto\Payment\PaymentCallbackDto;
use App\Dto\Payment\PaymentDto;

interface PaymentClientInterface
{

    /**
     * Виды транзакций
     *
     */

    /** платеж */
    public const CALLBACK_PAYMENT_TYPE_PAY = 'pay';
    /** возврат */
    public const CALLBACK_PAYMENT_TYPE_REFUND = 'refund';
    /** выплата */
    public const CALLBACK_PAYMENT_TYPE_PAYOUT = 'payout';

    /**
     * Статусы транзакций
     *
     */

    /** Ожидает оплаты покупателем */
    public const TRANSACTION_STATUS_PENDING = 'pending';
    /** Ожидает подтверждения магазином */
    public const TRANSACTION_STATUS_WAITING = 'waiting';
    /** Успешно оплачен и подтвержден магазином */
    public const TRANSACTION_STATUS_SUCCEEDED = 'succeeded';
    /** Неуспех оплаты или отменен магазином */
    public const TRANSACTION_STATUS_CANCELED = 'canceled';


    public function pay(PaymentDto $paymentDto): ?string;

    public function payout(PaymentDto $paymentDto): ?string;

    public function refund(PaymentDto $paymentDto): ?string;

    public function authorizeCallback(): void;

    public function getCallback(): PaymentCallbackDto;

    public function getWidget(string $_token, string $return_url): string;
}
