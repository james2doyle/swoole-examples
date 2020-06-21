<?php

use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Timer;
use Swoole\Table;

$host = getenv('HOST');
$hostname = getenv('HOSTNAME');
$port = getenv('PORT');

$server = new Server($host, $port);

$server->set([
    'worker_num' => 1,
    'open_tcp_keepalive' => 1, // open tcp_keepalive
    'tcp_keepidle' => 4, // 4s Test without data transmission
    'tcp_keepinterval' => 1, // 1s Detect once
    'tcp_keepcount' => 5, // Number of detections, no packet returned after more than 5 detections close This connection
]);

// a swoole server is evented just like express
$server->on('start', function (Server $server) use ($hostname, $port) {
    echo sprintf('Swoole http server is started at http://%s:%s' . PHP_EOL, $hostname, $port);
});

// handle all requests with this response
$server->on('request', function (Request $request, Response $response) use ($server) {
    $uri = $request->server['request_uri'];

    if ($uri === '/es') {
        // send the special headers required by the event stream protocol
        $response->header('Content-Type', 'text/event-stream');
        $response->header('Cache-Control', 'no-cache');
        $response->header('Connection', 'keep-alive');
        $response->header('X-Accel-Buffering', 'no');

        // send the initial ping message to start the event stream
        $response->write("event: ping" . PHP_EOL);

        // emulate events at 1 second intervals
        // this is where your app code would do its work and then `write` the response
        Timer::tick(1000, function () use ($server, $request, $response) {
            // make sure the connect exists before trying to send events
            if ($server->exists($request->fd)) {
                $curDate = date(DATE_ISO8601);
                $echo = 'data: {"time": "' . $curDate . '"}' . PHP_EOL . PHP_EOL;
                $response->write($echo);
            }
        });
    } else {
        $response->header('Content-Type', 'text/html');
        $echo = file_get_contents(__DIR__ . '/public/events.html');
        $response->end($echo);
    }

});

$server->start();
