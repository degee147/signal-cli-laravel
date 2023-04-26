<?php

require 'base.php';



$phone = $_POST['phone'] ?? '';
$message = $_POST['message'] ?? '';

$response = ['success' => false];

if (empty($phone) or empty($message)) {
    $response['output'] = "Phone and Message post variables are required";
} else {

    $response = $signal->sendMessage($phone, $message);
}

echo json_encode($response);