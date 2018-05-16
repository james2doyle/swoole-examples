<?php

use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;

$host = '0.0.0.0';
$hostname = getenv('HOSTNAME');
$port = getenv('PORT');

$server = new Server($host, $port);

// a swoole server is evented just like express
$server->on('start', function (Server $server) use ($hostname, $port) {
    echo sprintf('Swoole http server is started at http://%s:%s' . PHP_EOL, $hostname, $port);
});

// handle all requests with this response
$server->on('request', function (Request $request, Response $response) {
    $response->header('Content-Type', 'text/html');
    // "end" takes a string and sends it as the response
    $response->end(file_get_contents(__DIR__ . '/public/simple.html'));
});

$server->start();
