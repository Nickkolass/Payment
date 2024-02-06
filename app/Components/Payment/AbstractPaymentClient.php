<?php

namespace App\Components\Payment;

abstract class AbstractPaymentClient implements PaymentClientInterface
{

    public string $pay_url;

    abstract protected function getWidgetName(): string;

    public static function getConnection(): string
    {
        return config('payment.default');
    }

    public static function getClientName(): string
    {
        $connection = self::getConnection();
        return config("payment.connections.$connection.bind");
    }

    public static function getAgentLogin(): ?string
    {
        $connection = self::getConnection();
        return config("payment.connections.$connection.agent.login");
    }

    public static function getPaymentTypes(): array
    {
        return [
            self::CALLBACK_PAYMENT_TYPE_PAY,
            self::CALLBACK_PAYMENT_TYPE_REFUND,
            self::CALLBACK_PAYMENT_TYPE_PAYOUT,
        ];
    }

    public function getWidget(string $_token, string $return_url): string
    {
        return view($this->getWidgetName(), compact('_token', 'return_url'))->render();
    }
}

