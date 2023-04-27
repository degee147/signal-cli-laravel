<?php

require 'base.php';

$save_path = $signal->make_path("/receive");
$response = $signal->receiveMessages($save_path);

// if (!empty($response['output'])) {

// }

echo json_encode($response);
