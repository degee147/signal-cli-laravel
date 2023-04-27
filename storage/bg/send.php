<?php

require 'base.php';


echo "reading directory.." . PHP_EOL;

$dir = $signal->get_path("/send");
$sentresponses_path = $signal->make_path("/sentresponses");

$files = scandir($dir);

echo "found " . count($files) . " files including . and .." . PHP_EOL;

foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        // echo $file . "\n";
        $file_path = $dir . "/" . $file;
        $content = file_get_contents($file_path);
        $data = json_decode($content, true);
        $output = $signal->sendMessage($data['number'], $data['message'], $sentresponses_path);
        unlink($file_path);
        echo json_encode($output);

    }
}
