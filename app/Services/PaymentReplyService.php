<?php

namespace App\Services;

use App\Components\Transport\Consumer\ConsumerTransportnterface;
use App\Dto\Payment\PaymentCallbackDto;

class PaymentReplyService
{
    private ConsumerTransportnterface $transport;

    private function setStansport(string $requester_id): void
    {
        $transport_protokol = config("consumer.customers.$requester_id.reply_to_driver");
        $this->transport = app(config('consumer.drivers.' . $transport_protokol));
    }

    public function notify(PaymentCallbackDto $callbackDto, string $reply_to_cache_key): void
    {
        $requester_id = $this->getRequesterIdFromCache($reply_to_cache_key);
        $this->setStansport($requester_id);
        $this->transport->notify($callbackDto, $requester_id);
        $this->unsetRequesterIdFromCache($reply_to_cache_key);
    }

    public function requesterIdCaching(string $reply_to_cache_key, string $requester_id): void
    {
        cache()->forever(config('consumer.cache_prefix') . $reply_to_cache_key, $requester_id);
    }

    public function getRequesterIdFromCache(string $reply_to_cache_key): string
    {
        return cache()->get(config('consumer.cache_prefix') . $reply_to_cache_key);
    }

    public function unsetRequesterIdFromCache(string $reply_to_cache_key): void
    {
        cache()->forget(config('consumer.cache_prefix') . $reply_to_cache_key);
    }
}
