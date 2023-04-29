<?php

require 'base.php';

$status_file_name = "receiving.txt";
$status_file_path = $signal->get_status_file_path($status_file_name);

for ($i = 0; $i < 10; $i++) {
    if (file_exists($status_file_path)) {
        sleep(5);
    } else {
        break;
    }
}

$status = $signal->make_status_file($status_file_name);

for ($i = 0; $i < 4; $i++) {
    $save_path = $signal->make_path("receive");
    $response = $signal->receiveMessages($save_path);
    echo json_encode($response);
    sleep(2);
    $output = shell_exec('cd /home/ubuntu/laravel/ && php artisan app:signal');
    sleep(4);
}
unlink($status_file_path);

// sleep(2);
// $signal->readFiles();
