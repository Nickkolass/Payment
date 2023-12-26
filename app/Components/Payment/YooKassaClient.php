<?php

namespace App\Components\Payment;

use App\Dto\CallbackDto;
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

    public function __construct(public readonly Client $client)
    {
    }

    public function setAuth(bool $is_payout = false): void
    {
        $credentials = config('payment.connections.yookassa.' . ($is_payout ? 'agent' : 'shop'));
        $this->client->setAuth(...$credentials);
    }

    /**
     * @param array{order_id: int, price: int, return_url: string} $data
     * @return string
     */
    public function pay(array $data): string
    {
        $this->setAuth();
        $payment = $this->client->createPayment([
            'amount' => [
                'value' => $data['price'],
                'currency' => 'RUB',
            ],
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => $data['return_url'],
            ],
            'capture' => true,
            'description' => 'Оплата заказа №' . $data['order_id'],
            'metadata' => [
                'order_id' => $data['order_id'],
            ],
        ], uniqid('', true));

        return $payment->confirmation->getConfirmationUrl();
    }

    /**
     * @param array{order_id: int, price: int, payout_token: string} $data
     * @return void
     */
    public function payout(array $data): void
    {
        $this->setAuth(true);
        $this->client->createPayout([
            'amount' => [
                'value' => $data['price'],
                'currency' => 'RUB',
            ],
            'payout_token' => $data['payout_token'],
            'description' => 'Выплата по заказу №' . $data['order_id'],
            'metadata' => [
                'order_id' => $data['order_id'],
            ],
        ], uniqid('', true));
    }

    /**
     * @param array{order_id: int, pay_id:string, price:int} $data
     * @return void
     */
    public function refund(array $data): void
    {
        $this->setAuth();
        $this->client->createRefund([
            'payment_id' => $data['pay_id'],
            'amount' => [
                'value' => $data['price'],
                'currency' => 'RUB',
            ],
            'description' => 'Возврат по заказу №' . $data['order_id'],
        ], uniqid('', true));
    }

    public function authorizeCallback(): void
    {
        //проверка на соответствие ip входящего уведомления от платежной системы
//        $ips = ['185.71.76.0/27', '185.71.77.0/27', '77.75.153.0/25', '77.75.156.11', '77.75.156.35', '77.75.154.128/25', '2a02:5180::/32'];
//        if (!in_array(request()->ip(), $ips)) abort(403);
    }

    public function getWidget(): string
    {
        return self::WIDGET_VIEW;
    }

    public function getCallback(): CallbackDto
    {
        $source = file_get_contents('php://input');
        $requestBody = json_decode((string)$source, true);
        $notification = $this->getNotification($requestBody);
        $transaction = $notification->getObject();

        return new CallbackDto(
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
//            NotificationEventType::DEAL_CLOSED => new NotificationDealClosed($requestBody),
            default => null,
        };
        if (is_null($notification)) abort(400);
        return $notification;
    }

    private function statusAdapter(?string $status): string
    {
        $status = match ($status) {
//            PaymentStatus::PENDING => self::TRANSACTION_STATUS_PENDING,
//            PaymentStatus::WAITING_FOR_CAPTURE => self::TRANSACTION_STATUS_WAITING,
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
            ReceiptType::PAYMENT => self::CALLBACK_EVENT_PAY,
            ReceiptType::REFUND => self::CALLBACK_EVENT_REFUND,
            SettlementPayoutPaymentType::PAYOUT => self::CALLBACK_EVENT_PAYOUT,
//            'deal' => self::CALLBACK_EVENT_DEAL,
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
        if (!is_int($id)) abort(400);
        return $id;
    }
}

