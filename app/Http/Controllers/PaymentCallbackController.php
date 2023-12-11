<?php

namespace App\Http\Controllers;

use App\Services\PaymentCallbackService;
use Illuminate\Http\Response;

class PaymentCallbackController extends Controller
{

    public function __construct(public readonly PaymentCallbackService $callbackService)
    {
    }

    /** Уведомления от платежной системы */
    public function callback(): Response
    {
        $this->callbackService->callback();
        return response(status: 200)->send();
    }
}
