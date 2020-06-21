<?php

use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Table;

$messages = new Table(1024);
$messages->column('id', Table::TYPE_INT, 11);
$messages->create();

$host = getenv('HOST');
$hostname = getenv('HOSTNAME');
$port = getenv('PORT');

$server = new Server($host, $port);

$server->set([
    'dispatch_mode' => 7,
    'worker_num' => 2,
    'keep_alive' => true,
    'open_tcp_keepalive' => 1,
    'tcp_keepidle' => 4, // Corresponding tcp_keepalive_time
    'tcp_keepinterval' => 1, // Corresponding tcp_keepalive_intvl
    'tcp_keepcount' => 5, // Corresponding tcp_keepalive_probes
]);

// a swoole server is evented just like express
$server->on('start', function (Server $server) use ($hostname, $port) {
    echo sprintf('Swoole http server is started at http://%s:%s' . PHP_EOL, $hostname, $port);
});

// handle all requests with this response
$server->on('request', function (Request $request, Response $response) use ($messages) {
    $uri = $request->server['request_uri'];

    if ($uri === '/es') {
        $response->header('Content-Type', 'text/event-stream');
        $response->header('Cache-Control', 'no-cache');
        $response->header('Connection', 'keep-alive');
        $response->header('X-Accel-Buffering', 'no');
        if (count($messages) < 1) {
            $messages->set(time(), ['id' => $request->fd]);
            $echo = "event: ping" . PHP_EOL;
        } else {
            $curDate = date(DATE_ISO8601);
            $echo = 'data: {"time": "' . $curDate . '"}' . PHP_EOL . PHP_EOL;
        }
    } else {
        $response->header('Content-Type', 'text/html');
        $echo = file_get_contents(__DIR__ . '/public/events.html');
    }

    $response->end($echo);
});

$server->start();
