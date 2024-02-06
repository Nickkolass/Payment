<?php

namespace App\Http\Controllers;

use App\Dto\Payment\PaymentDto;
use App\Http\Request\CardValidateRequest;
use App\Http\Request\PaymentRequest;
use App\Http\Request\WidgetRequest;
use App\Services\PaymentReplyService;
use App\Services\PaymentService;

class PaymentController extends Controller
{

    public function __construct(
        private readonly PaymentService      $paymentService,
        private readonly PaymentReplyService $replyService,
    )
    {
    }

    public function getWidget(WidgetRequest $request): string
    {
        $data = $request->validated();
        return $this->paymentService->getRenderedWidget($data['_token'], $data['return_url']);
    }

    public function cardValidate(CardValidateRequest $request): void
    {
    }

    public function payment(PaymentRequest $request): void
    {
        $data = $request->validated();
        $method = $data['payment_type'];
        $payment_id = $this->paymentService->$method(new PaymentDto(...$data));
        $this->replyService->requesterIdCaching($payment_id, $request->headers->get('requester-id'));
        if (config('payment.default') == 'stub') $this->callback();
    }

    /** Уведомления от платежной системы */
    public function callback(): void
    {
        $callbackDto = $this->paymentService->callback();
        if ($callbackDto) $this->replyService->notify($callbackDto, $callbackDto->id);
    }
}
