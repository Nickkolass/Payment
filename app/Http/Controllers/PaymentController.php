<?php

namespace App\Http\Controllers;

use App\Components\Payment\PaymentClientInterface;
use App\Http\Request\Card\ValidateRequest;
use App\Http\Request\Card\WidgetRequest;
use App\Http\Request\Payment\PayoutRequest;
use App\Http\Request\Payment\PayRequest;
use App\Http\Request\Payment\RefundRequest;
use Illuminate\Http\Response;

class PaymentController extends Controller
{

    public function __construct(public readonly PaymentClientInterface $paymentClient)
    {
    }

    public function getWidget(WidgetRequest $request): string
    {
        $data = $request->validated();
        $view = $this->paymentClient->getWidget();
        return view('widget.yookassa', compact('data'))->render();
    }

    public function cardValidate(ValidateRequest $request): Response
    {
        return response('', 200)->send();
    }

    public function pay(PayRequest $request): string
    {
        $data = $request->validated();
        return $this->paymentClient->pay($data);
    }

    public function refund(RefundRequest $request): void
    {
        $data = $request->validated();
        $this->paymentClient->refund($data);
    }

    public function payout(PayoutRequest $request): void
    {
        $data = $request->validated();
        $this->paymentClient->payout($data);
    }
}
