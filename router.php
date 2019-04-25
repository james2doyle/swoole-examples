<?php

require_once __DIR__ . '/vendor/autoload.php';

use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;

use FastRoute\RouteCollector;

// handle get index requests
function get_index_handler(array $vars)
{
    return [
        'status' => 200,
        'message' => 'Hello world!',
        'vars' => [
            'vars' => $vars,
            '$_GET' => $_GET,
            '$_POST' => $_POST,
        ],
    ];
}

// handle get index requests
function post_index_handler(array $vars)
{
    return [
        'status' => 200,
        'message' => 'Hello world!',
        'vars' => [
            'vars' => $vars,
            '$_GET' => $_GET,
            '$_POST' => $_POST,
        ],
    ];
}

$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) {
    $r->addRoute('GET', '/[{title}]', 'get_index_handler');
    $r->addRoute('POST', '/[{title}]', 'post_index_handler');
});

function handleRequest($dispatcher, string $request_method, string $request_uri)
{
    list($code, $handler, $vars) = $dispatcher->dispatch($request_method, $request_uri);

    switch ($code) {
        case FastRoute\Dispatcher::NOT_FOUND:
            $result = [
                'status' => 404,
                'message' => 'Not Found',
                'errors' => [
                    sprintf('The URI "%s" was not found', $request_uri)
                ]
            ];
            break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $allowedMethods = $handler;
            $result = [
                'status' => 405,
                'message' => 'Method Not Allowed',
                'errors' => [
                    sprintf('Method "%s" is not allowed', $request_method)
                ]
            ];
            break;
        case FastRoute\Dispatcher::FOUND:
            $result = call_user_func($handler, $vars);
            break;
    }

    return $result;
}

$host = getenv('HOST');
$hostname = getenv('HOSTNAME');
$port = getenv('PORT');

$server = new Server($host, $port);

// a swoole server is evented just like express
$server->on('start', function (Server $server) use ($hostname, $port) {
    echo sprintf('Swoole http server is started at http://%s:%s' . PHP_EOL, $hostname, $port);
});

// handle all requests with this response
$server->on('request', function (Request $request, Response $response) use ($dispatcher) {
    $request_method = $request->server['request_method'];
    $request_uri = $request->server['request_uri'];

    // populate the global state with the request info
    $_SERVER['REQUEST_URI'] = $request_uri;
    $_SERVER['REQUEST_METHOD'] = $request_method;
    $_SERVER['REMOTE_ADDR'] = $request->server['remote_addr'];

    $_GET = $request->get ?? [];
    $_FILES = $request->files ?? [];

    // form-data and x-www-form-urlencoded work out of the box so we handle JSON POST here
    if ($request_method === 'POST' && $request->header['content-type'] === 'application/json') {
        $body = $request->rawContent();
        $_POST = empty($body) ? [] : json_decode($body);
    } else {
        $_POST = $request->post ?? [];
    }

    // global content type for our responses
    $response->header('Content-Type', 'application/json');

    $result = handleRequest($dispatcher, $request_method, $request_uri);

    // write the JSON string out
    $response->end(json_encode($result));
});

$server->start();
