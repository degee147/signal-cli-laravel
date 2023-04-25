<?php

namespace App\Console\Commands;

use App\Services\SignalService;
use Illuminate\Console\Command;

class signal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:signal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interact with Signal CLI';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $argument = $this->argument('argument');
        // $option = $this->option('option');
        // php artisan app:signal
        $updates = (new SignalService())->receiveMessages();
        echo json_encode($updates);

    }
}
