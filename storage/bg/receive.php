<?php

require 'base.php';

$save_path = $signal->make_path("/receive");
$response = $signal->receiveMessages($save_path);

sleep(2);

// $signal->readFiles();

$output = shell_exec('cd /home/ubuntu/laravel/ && php artisan app:signal');


echo json_encode($response);
