<?php

namespace App\Console\Commands;

use App\Services\SignalService;
use Illuminate\Console\Command;

class ClearRead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-read {phone}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make Db lean by clearing handled messages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $phone = $this->argument('phone');
        (new SignalService())->deleteReplied($phone);
        echo "cleared";

    }
}
