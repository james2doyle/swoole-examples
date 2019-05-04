<?php

require_once __DIR__ . '/vendor/autoload.php';

use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;

use Slim\App;

$host = getenv('HOST');
$hostname = getenv('HOSTNAME');
$port = getenv('PORT');

$server = new Server($host, $port);

// a swoole server is evented just like express
$server->on('start', function (Server $server) use ($hostname, $port) {
    echo sprintf('Swoole http server is started at http://%s:%s' . PHP_EOL, $hostname, $port);
});

// handle all requests with this response
$server->on('request', function (Request $req, Response $res) {
    // populate the global state with the request info
    $_SERVER['REQUEST_URI'] = $req->server['request_uri'];
    $_SERVER['REQUEST_METHOD'] = $req->server['request_method'];
    $_SERVER['REMOTE_ADDR'] = $req->server['remote_addr'];

    $_GET = $req->get ?? [];
    $_POST = $req->post ?? $req->rawContent();
    $_FILES = $req->files ?? [];

    // each request should create a new App()
    $app = new App();

    // example of a JSON response
    $app->get('/type/json', function ($request, $response, $args) {
        return $response->withJson([
            'status' => 'ok',
            'message' => 'hey!'
        ]);
    });

    // normal text/html response
    $app->get('/[{name}]', function ($request, $response, $args) {
        $name = $args['name'] ?? 'world!';

        return $response
            ->getBody()
            ->write(sprintf('<p>Hello, %s</p>', $name));
    });

    // suppress output by passing "true"
    $slim = $app->run(true);

    // transfer the Slim headers to the Swoole app
    foreach ($slim->getHeaders() as $key => $value) {
        // content length is set when calling "end"
        if ($key !== 'Content-Length') {
            $res->header($key, $value[0]);
        }
    }

    $res->status($slim->getStatusCode());

    // figure out if we are running in HTTPS
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443);

    // just add a make pretend cookie here
    $res->cookie(explode('.', 'docker.local')[0], '1', strtotime('+1 day'), '/', getenv('HOSTNAME'), $secure , true);

    // write the output
    $res->end($slim->getBody());
});

$server->start();
