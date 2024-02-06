<?php

namespace App\Providers;

use App\Components\Payment\AbstractPaymentClient;
use App\Components\Payment\PaymentClientInterface;
use App\Components\Transport\TransportServiceProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(TransportServiceProvider::class);
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
