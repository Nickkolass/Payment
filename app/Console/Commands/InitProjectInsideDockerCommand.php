<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InitProjectInsideDockerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'init project inside docker container';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle(): void
    {
        $ngrok_token = config('services.ngrok.auth_token');
        if($ngrok_token) echo(shell_exec('./ngrok customers add-authtoken ' . $ngrok_token) . PHP_EOL);
        $this->call('key:generate', ['--force' => true]);
        $this->call('optimize:clear');
        $this->call('optimize');
    }
}
