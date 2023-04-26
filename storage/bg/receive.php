<?php

require 'base.php';

$response = $signal->receiveMessages();

if (!empty($response['output'])) {

}

echo json_encode($response);
