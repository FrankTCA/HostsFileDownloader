<?php
require "util.php";
$util = new Util();

// First: download the file
$util->downloadWrapper();

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="hosts.txt"');
header('Content-Length: ' . filesize("./hosts"));
header('Pragma: public');

flush();

readfile("./hosts", true);
die();
