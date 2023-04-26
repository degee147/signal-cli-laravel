<?php

require 'base.php';


$output = $signal->version();
echo json_encode($output);
