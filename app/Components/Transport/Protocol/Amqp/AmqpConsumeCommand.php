<?php

namespace App\Components\Transport\Protocol\Amqp;

use Illuminate\Console\Command;

class AmqpConsumeCommand extends Command
{
    protected $signature = 'amqp:consume {consumer_id}';

    protected $description = 'amqp consume';

    public function __construct(private readonly AmqpClientInterface $amqp)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        echo " [*] Waiting for messages. To exit press CTRL+C\n";
        $this->amqp->consume((string)$this->argument('consumer_id'));
    }
}
