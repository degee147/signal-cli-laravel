<?php

require 'base.php';

$output = $signal->receiveMessages();
echo json_encode($output);