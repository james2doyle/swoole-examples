<?php

use Illuminate\Http\Request;
use SwooleTW\Http\Websocket\Facades\Websocket;

/*
|--------------------------------------------------------------------------
| Websocket Routes
|--------------------------------------------------------------------------
|
| Here is where you can register websocket events for your application.
|
*/

Websocket::on('connect', function ($websocket, Request $request) {
    // in connect callback, illuminate request will be injected here
    $websocket->emit('message', 'welcome');

    \Swoole\Timer::tick(1000, function () use ($websocket) {
        $websocket->emit('message', "⚙️ Do something... " . time());
    });
});

Websocket::on('disconnect', function ($websocket) {
    // this callback will be triggered when a websocket is disconnected
});

Websocket::on('example', function ($websocket, $data) {
    $websocket->emit('message', $data);
});
