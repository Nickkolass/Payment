<?php

namespace App\Components\Transport\Consumer;


use App\Dto\Payment\PaymentCallbackDto;

interface ConsumerTransportnterface
{

    public function notify(PaymentCallbackDto $callbackDto, string $requester_id): void;

}
