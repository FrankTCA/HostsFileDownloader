<?php
require "util.php";
$util = new Util();

$util->downloadWrapper();
$util->unbound_change();

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="hosts.txt"');
header('Content-Length: ' . filesize("./hosts"));
header('Pragma: public');

flush();
readfile("./unbound", true);
die();
