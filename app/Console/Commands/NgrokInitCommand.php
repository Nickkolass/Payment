<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class NgrokInitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ngrok:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ngrok init';

    /**
     * Execute the console command.
     *
     */
    public function handle(): void
    {
        echo(shell_exec('./ngrok config add-authtoken ' . config('services.ngrok.auth_token')));
    }
}
