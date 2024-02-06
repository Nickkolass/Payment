<?php

namespace App\Components\Transport;

use App\Components\Transport\Protocol\Amqp\AmqpClientInterface;
use App\Components\Transport\Protocol\Amqp\AmqpConsumeCommand;
use App\Components\Transport\Protocol\Http\HttpClientInterface;
use Illuminate\Support\ServiceProvider;

class TransportServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->registerTransport();
        $this->registerConsumers();
    }

    public function boot(): void
    {
        $this->bootTransport();
        $this->bootConsumers();
    }

    public function provides(): array
    {
        return [
            AmqpClientInterface::class,
            HttpClientInterface::class,
        ];
    }

    private function registerTransport(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/Protocol/config.php', 'transport');

        $this->app->bind(AmqpClientInterface::class, function () {
            $default_amqp = config('transport.protocols.amqp.default');
            return $this->app->make(config("transport.protocols.amqp.clients.$default_amqp.bind"));
        });
        $this->app->bind(HttpClientInterface::class, function () {
            $default_http = config('transport.protocols.http.default');
            return $this->app->make(config("transport.protocols.http.clients.$default_http.bind"));
        });
    }

    private function registerConsumers(): void
    {
        $key = 'consumer';
        $this->mergeConfigFrom(__DIR__ . '/Consumer/config.php', $key);

        foreach ((array)glob(__DIR__ . '/Consumer/customers/*.php') as $config) {
            $config_key = explode('/', $config);
            $config_key = $key . '.customers.' . str_replace('.php', '', array_pop($config_key));
            $this->mergeConfigFrom($config, $config_key);
        }
    }

    private function bootTransport(): void
    {
        $this->commands(AmqpConsumeCommand::class);
    }

    private function bootConsumers(): void
    {
    }
}
