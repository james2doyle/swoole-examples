<?php

use Swoole\Process;
use Swoole\Event;

$workers = [];
$worker_num = 3; // 10;

for ($i = 0; $i < $worker_num; $i++) {
    $process = new Process(function ($worker) {
        // execute the external program on external file
        $worker->exec("/usr/local/bin/php", [__DIR__ . '/echo.php']);
    }, true); // redirect stdin
    $pid = $process->start();
    $process->write("hello worker " . $i . " in process " . $pid);
    $workers[$pid] = $process;
}

foreach ($workers as $process) {
    Event::add($process->pipe, function ($pipe) use ($process) {
        // read the data from stdout of the process
        $data = $process->read();
        echo "Worker message: " . $data . " from " . $pipe . PHP_EOL;
        Event::del($process->pipe);
    });
}

Event::wait();
