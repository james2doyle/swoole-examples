<?php

$fd = fopen('php://stdin', 'r');
$res = fread($fd, 128);
echo "Parent message: " . $res;
