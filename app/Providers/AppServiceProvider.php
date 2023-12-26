<?php

namespace App\Providers;

use App\Components\Payment\AbstractPaymentClient;
use App\Components\Payment\PaymentClientInterface;
use App\Components\Payment\StubClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentClientInterface::class, AbstractPaymentClient::getClientName());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
