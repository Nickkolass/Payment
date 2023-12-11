<?php

namespace App\Dto;

class CallbackDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $event,
        public readonly string $status,
        public readonly int    $order_id,
    )
    {
    }
}
