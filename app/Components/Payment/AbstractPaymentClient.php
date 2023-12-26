<?php

namespace App\Components\Payment;

use App\Dto\CallbackDto;
use Illuminate\Support\Facades\Http;

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

    public static function getClientName(): ?string
    {
        $connection = self::getConnection();
        return config("payment.connections.{$connection}.bind");
    }

    public static function getAgentLogin(): ?string
    {
        $connection = self::getConnection();
        return config("payment.connections.{$connection}.agent.login");
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

    public function sendCallbackNotification(CallbackDto $callbackDto): void
    {
        if ($callbackDto->status == self::TRANSACTION_STATUS_SUCCEEDED) {
            for ($i = 3; $i <= 9; $i+=2) {
                $status =  Http::post('host.docker.internal:8876/api/payment/callback', (array)$callbackDto)->status();
                if($status == 200) break;
                $fib = round(1.618 ** $i / 2.236);
                sleep($fib);
            }
        } else \Log::info((string)json_encode($callbackDto));
    }
}

