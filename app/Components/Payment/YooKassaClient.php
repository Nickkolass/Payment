<?php

namespace App\Components\Payment;

use App\Dto\Payment\PaymentCallbackDto;
use App\Dto\Payment\PaymentDto;
use YooKassa\Client;
use YooKassa\Model\Deal\DealInterface;
use YooKassa\Model\Deal\SettlementPayoutPaymentType;
use YooKassa\Model\Notification\NotificationCanceled;
use YooKassa\Model\Notification\NotificationEventType;
use YooKassa\Model\Notification\NotificationInterface;
use YooKassa\Model\Notification\NotificationPayoutCanceled;
use YooKassa\Model\Notification\NotificationPayoutSucceeded;
use YooKassa\Model\Notification\NotificationRefundSucceeded;
use YooKassa\Model\Notification\NotificationSucceeded;
use YooKassa\Model\Payment\PaymentInterface;
use YooKassa\Model\Payment\PaymentStatus;
use YooKassa\Model\Payout\PayoutInterface;
use YooKassa\Model\Receipt\ReceiptType;
use YooKassa\Model\Refund\RefundInterface;

class YooKassaClient extends AbstractPaymentClient
{

    public const WIDGET_VIEW = 'widget.yookassa';

    private readonly Client $client;

    private function setAuth(bool $is_payout = false): void
    {
        $this->client = new Client();
        $credentials = config('payment.connections.yookassa.' . ($is_payout ? 'agent' : 'shop'));
        $this->client->setAuth(...$credentials);
    }

    public function pay(PaymentDto $paymentDto): ?string
    {
        $this->setAuth();
        $pay = $this->client->createPayment([
            'amount' => [
                'value' => $paymentDto->price,
                'currency' => 'RUB',
            ],
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => $paymentDto->return_url,
            ],
            'capture' => true,
            'description' => 'Оплата заказа №' . $paymentDto->order_id,
            'metadata' => [
                'order_id' => $paymentDto->order_id,
            ],
        ], uniqid('', true));
        $this->pay_url = $pay->getConfirmation()->getConfirmationUrl();
        return $pay->getId();
    }

    public function payout(PaymentDto $paymentDto): ?string
    {
        $this->setAuth(true);
        $payout = $this->client->createPayout([
            'amount' => [
                'value' => $paymentDto->price,
                'currency' => 'RUB',
            ],
            'payout_token' => $paymentDto->payout_token,
            'description' => 'Выплата по заказу №' . $paymentDto->order_id,
            'metadata' => [
                'order_id' => $paymentDto->order_id,
            ],
        ], uniqid('', true));
        return $payout->getId();
    }

    public function refund(PaymentDto $paymentDto): ?string
    {
        $this->setAuth();
        $refund = $this->client->createRefund([
            'payment_id' => $paymentDto->pay_id,
            'amount' => [
                'value' => $paymentDto->price,
                'currency' => 'RUB',
            ],
            'description' => 'Возврат по заказу №' . $paymentDto->order_id,
        ], uniqid('', true));
        return $refund->getId();
    }

    protected function getWidgetName(): string
    {
        return self::WIDGET_VIEW;
    }

    public function authorizeCallback(): void
    {
        //проверка на соответствие ip входящего уведомления от платежной системы
//        $ips = ['185.71.76.0/27', '185.71.77.0/27', '77.75.153.0/25', '77.75.156.11', '77.75.156.35', '77.75.154.128/25', '2a02:5180::/32'];
//        if (!in_array(request()->ip(), $ips)) abort(403);
    }

    public function getCallback(): PaymentCallbackDto
    {
        $source = file_get_contents('php://input');
        $requestBody = json_decode((string)$source, true);
        $notification = $this->getNotification($requestBody);
        $transaction = $notification->getObject();

        return new PaymentCallbackDto(
            id: $transaction->id,
            event: $this->eventAdapter($notification->getEvent()),
            status: $this->statusAdapter($transaction->getStatus()),
            order_id: $this->getOrderId($transaction),
        );
    }

    private function getNotification(mixed $requestBody): NotificationInterface
    {
        $notification = match ($requestBody['event']) {
//            NotificationEventType::PAYMENT_WAITING_FOR_CAPTURE => new NotificationWaitingForCapture($requestBody),
            NotificationEventType::PAYMENT_SUCCEEDED => new NotificationSucceeded($requestBody),
            NotificationEventType::PAYMENT_CANCELED => new NotificationCanceled($requestBody),
            NotificationEventType::REFUND_SUCCEEDED => new NotificationRefundSucceeded($requestBody),
            NotificationEventType::PAYOUT_SUCCEEDED => new NotificationPayoutSucceeded($requestBody),
            NotificationEventType::PAYOUT_CANCELED => new NotificationPayoutCanceled($requestBody),
            default => null,
        };
        if (is_null($notification)) abort(400);
        return $notification;
    }

    private function statusAdapter(?string $status): string
    {
        $status = match ($status) {
            PaymentStatus::PENDING => self::TRANSACTION_STATUS_PENDING,
            PaymentStatus::WAITING_FOR_CAPTURE => self::TRANSACTION_STATUS_WAITING,
            PaymentStatus::SUCCEEDED => self::TRANSACTION_STATUS_SUCCEEDED,
            PaymentStatus::CANCELED => self::TRANSACTION_STATUS_CANCELED,
            default => null,
        };
        if (is_null($status)) abort(400);
        return $status;
    }

    private function eventAdapter(?string $event): string
    {
        $event = match (explode('.', $event)[0]) {
            ReceiptType::PAYMENT => self::CALLBACK_PAYMENT_TYPE_PAY,
            ReceiptType::REFUND => self::CALLBACK_PAYMENT_TYPE_REFUND,
            SettlementPayoutPaymentType::PAYOUT => self::CALLBACK_PAYMENT_TYPE_PAYOUT,
            default => null,
        };
        if (is_null($event)) abort(400);
        return $event;
    }

    private function getOrderId(RefundInterface|PaymentInterface|PayoutInterface|DealInterface $transaction): int
    {
        $id = $transaction->metadata->order_id
            ?? str_replace('Возврат по заказу №', '', $transaction->description)
            ?? null;
        if (is_null($id)) abort(400);
        return $id;
    }
}

