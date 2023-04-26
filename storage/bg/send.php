<?php

require 'base.php';


$output = $signal->sendMessage("+2348030910338", "Hi there, how are you?");
echo json_encode($output);
