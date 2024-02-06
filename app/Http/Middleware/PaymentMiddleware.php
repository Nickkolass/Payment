<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        //проверка на соответствие ip входящего уведомления от платежной системы
        return $next($request);
    }
}
