<?php

require 'base.php';


//generate captcha from https://signalcaptchas.org/registration/generate.html
// or from https://signalcaptchas.org/challenge/generate.html
// $captcha = file_get_contents(__DIR__ . "/captcha.txt");
$captcha = $_POST['captcha'] ?? '';

$response = ['success' => false];

if (empty($captcha)) {
    $response['output'] = "The captcha field is required. Generate Captcha from https://signalcaptchas.org/registration/generate.html or from https://signalcaptchas.org/challenge/generate.html";
} else {

    $response = $signal->register($captcha);
}

echo json_encode($response);