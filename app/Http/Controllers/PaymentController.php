<?php

namespace App\Http\Controllers;

use App\Http\Request\Card\WidgetRequest;
use App\Http\Request\Card\ValidateRequest;
use App\Http\Request\Payment\PayoutRequest;
use App\Http\Request\Payment\PayRequest;
use App\Http\Request\Payment\RefundRequest;
use App\Http\Resources\CardResource;
use App\Services\PaymentService;

class PaymentController extends Controller
{

    public function __construct(public readonly PaymentService $paymentService)
    {
    }

    public function getWidget(WidgetRequest $request): string
    {
        $data = $request->validated();
        $view = $this->paymentService->paymentClient->getWidget();
        return view($view, compact('data'))->render();
    }

    public function cardValidate(ValidateRequest $request): CardResource
    {
        $card = $request->validated()['card'];
        return new CardResource($card);
    }

    public function pay(PayRequest $request): string
    {
        $data = $request->validated();
        return $this->paymentService->pay($data);
    }

    public function refund(RefundRequest $request): void
    {
        $data = $request->validated();
        $this->paymentService->refund($data);
    }

    public function payout(PayoutRequest $request): void
    {
        $data = $request->validated();
        $this->paymentService->payout($data);
    }
}
