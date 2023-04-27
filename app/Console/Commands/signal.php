<?php

namespace App\Console\Commands;

use App\Models\Message;
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
        // Artisan::call('app:signal');
        // $argument = $this->argument('argument');
        // $option = $this->option('option');
        // php artisan app:signal
        // $updates = (new SignalService())->receiveMessages();
        // echo json_encode($updates);
        // $this->saveMessages();

        // Message::truncate();
        $this->readFiles();

    }

    private function readFiles()
    {
        echo "reading directory.." . PHP_EOL;

        $dir = storage_path() . '/bg/receive'; // replace with the directory you want to scan

        $files = scandir($dir);

        echo "found " . count($files) . " files including . and .." . PHP_EOL;

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                // echo $file . "\n";
                $file_path = $dir . "/" . $file;
                $response = (new SignalService())->saveMessages($file_path);
                if ($response['success']) {
                    echo $response['output'] . PHP_EOL;
                }

                if (file_exists($file_path) and ($response['count'] > 0)) {
                    unlink($file_path);
                    echo "File deleted successfully." . PHP_EOL;
                }
            }
        }
    }
    private function saveMessages()
    {

        $dir = storage_path() . '/bg/receive'; // replace with the directory you want to scan

        $files = scandir($dir);

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                // echo $file . "\n";
                $file_path = $dir . "/" . $file;
                $output = file_get_contents($file_path);
                if (!empty($output)) {
                    $response = (new SignalService())->saveMessages($output);
                    if ($response['success']) {
                        echo $response['output'] . PHP_EOL;
                    }
                }
                if (file_exists($file_path)) {
                    unlink($file_path);
                    echo "File deleted successfully." . PHP_EOL;
                }
            }
        }

    }
}
