<?php

require 'base.php';

$save_path = make_path("/sentresponses");
$output = $signal->sendMessage("+2348030910338", "Hi there, new message to send", $save_path);
echo json_encode($output);
