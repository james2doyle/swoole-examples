<?php

use Swoole\Async;
use Swoole\Http\Client;

// pick a DNS resolver first - we chose Google Public DNS
Async::set(['dns_server' => '8.8.8.8']);

// DNS lookup would block the process - so we go async
Async::dnsLookup('example.com', function ($domain, $ip) {
    // Client can only take an IP so we need to resolve it first
    $client = new Client($ip, 80);

    $client->setHeaders([
        'Host' => $domain, // which host?
        'User-Agent' => 'swoole-http-client',
        'Accept' => 'text/html,application/xhtml+xml,application/xml',
        'Accept-Encoding' => 'gzip',
    ]);

    // now we can call methods on our client and pass URIs
    $client->get('/index.html', function ($response) {
        $out_file = sprintf(__DIR__ . '/tmp/output-%s.html', time());
        Async::writefile($out_file, $response->body, function ($filename) {
            echo "write {$filename} ok\n";
            // we can close the server now
            exit();
        });
    });
});
