<?php

namespace App\Components\Transport\Protocol;

interface TransportInterface
{
    public function publish(): void;
}
