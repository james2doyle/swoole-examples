<?php

use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;

$host = getenv('HOST');
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
    // Before the call of this method, it has to set content type by $response->header()
    // Before the call of this method, it must not call $response->write
    // After the call of this method, it will call $response->end() automatically
    $response->sendfile(__DIR__ . '/public/simple.html');
});

$server->start();
