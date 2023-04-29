<?php

require 'base.php';

$status_file_name = "sending.txt";
$status_file_path = $signal->get_status_file_path($status_file_name);

for ($i = 0; $i < 10; $i++) {
    if (file_exists($status_file_path)) {
        sleep(5);
    } else {
        break;
    }
}

$status = $signal->make_status_file($status_file_name);


echo "reading directory.." . PHP_EOL;

$dir = $signal->get_path("send");


for ($i = 0; $i < 30; $i++) {

    $files = scandir($dir);
    echo "found " . count($files) . " files including . and .." . PHP_EOL;

    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            // echo $file . "\n";
            $file_path = $dir . "/" . $file;
            if (file_exists($file_path)) {
                $sentresponses_path = $signal->make_path("sentresponses");
                $content = file_get_contents($file_path);
                unlink($file_path);
                $data = json_decode($content, true);
                $output = $signal->sendMessage($data['number'], $data['message'], $sentresponses_path);

                if ($output['success']) {
                    sleep(2);
                    //keep DB lean
                    //this won't work. Model is not available from command line except through artisan
                    // $signal->deleteReplied($data['number']);
                    shell_exec('cd /home/ubuntu/laravel/ && php artisan app:clear-read ' . $data['number']);
                }
                echo json_encode($output);
            }
        }
    }

    sleep(5);

}
unlink($status_file_path);
sleep(3);

// clear sent messages responses
$sentresponses_path = $signal->get_path("sentresponses");
$files = scandir($dir);
foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        $file_path = $dir . "/" . $file;
        unlink($file_path);
    }
}
