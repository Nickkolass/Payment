<?php

namespace App\Components\Payment;

abstract class AbstractPaymentClient implements PaymentClientInterface
{

    /**
     * Виды входящих уведомлений
     *
     */

    /** платеж */
    public const CALLBACK_EVENT_PAY = 'pay';
    /** возврат */
    public const CALLBACK_EVENT_REFUND = 'refund';
    /** выплата */
    public const CALLBACK_EVENT_PAYOUT = 'payout';
    /** сделка */
    public const CALLBACK_EVENT_DEAL = 'deal';

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

    public static function getConnection(): ?string
    {
        return config('payment.default');
    }

    public static function getAgentLogin(): ?string
    {
        return config('payment.connections.' . self::getConnection() . '.agent.login');
    }

    /**
     * @return array<string>
     */
    public static function getStatuses(): array
    {
        return [
            self::TRANSACTION_STATUS_PENDING,
            self::TRANSACTION_STATUS_WAITING,
            self::TRANSACTION_STATUS_SUCCEEDED,
            self::TRANSACTION_STATUS_CANCELED,
        ];
    }

    /**
     * @return array<string>
     */
    public static function getCallbackEvents(): array
    {
        return [
            self::CALLBACK_EVENT_PAY,
            self::CALLBACK_EVENT_REFUND,
            self::CALLBACK_EVENT_PAYOUT,
            self::CALLBACK_EVENT_DEAL,
        ];
    }

}

