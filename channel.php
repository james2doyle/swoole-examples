<?php

use Swoole\Channel as chan;
use Swoole\Coroutine as co;

// channels are used to communicate through coroutines
$out = new chan(1);

for ($i = 0; $i < 4; $i++) {
    // go function is a coroutine
    go(function () use ($i, $out) {
        // sleep before populating the channel
        co::sleep(2);
        $out->push("1-{$i}");
        $out->push("2-{$i}");
    });
    go(function () use ($out) {
        $data = $out->pop();
        echo $data . PHP_EOL;
    });
}
