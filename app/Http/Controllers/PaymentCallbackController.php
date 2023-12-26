<?php

namespace App\Http\Controllers;

use App\Http\Services\PaymentCallbackService;
use Illuminate\Http\Response;

class PaymentCallbackController extends Controller
{

    public function __construct(public readonly PaymentCallbackService $service)
    {
    }

    /** Уведомления от платежной системы */
    public function callback(): Response
    {
        $this->service->callback();
        return response(status: 200)->send();
    }
}
