<?php

require 'base.php';

$save_path = make_path("/receive");
$response = $signal->receiveMessages($save_path);

// if (!empty($response['output'])) {

// }

echo json_encode($response);
