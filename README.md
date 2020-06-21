Swoole Example Projects
=========================

> Examples on how to use the [swoole async PHP framework](https://www.swoole.co.uk/)

### Examples Inside

* `serve-file`: server a file using the correct mimetype
* `client`: make an async call to an external domain and download the page
* `channel`: share memory and data between coroutines
* `process`: load an external program and run it multiple processes
* `router`: use an off-the-rack router ([nikic/fast-route](https://github.com/nikic/FastRoute)) to route request methods and URIs
* `websocket`: a chat example with websockets
* `slim`: an example of how to use the [Slim Framework](https://www.slimframework.com/)
* `users`: using Swoole with Laravel with the [laravel-swoole/wiki/Z1.-Notices](https://github.com/swooletw/laravel-swoole/)
* `event-source`: an example of the [EventSource](https://developer.mozilla.org/en-US/docs/Web/API/EventSource) long-lived connection

### Installation

Take a look inside `docker-compose.yml` and uncomment the example you want to run.

* `composer install`
* `docker-compose up`

#### Installation for Laravel example

* `cd users`
* `composer install`
* `cp .env.example .en`
* Update `DB_CONNECTION=sqlite`
* `touch database/database.sqlite`
* `php artisan key:generate`
* `php artisan migrate`
* `php artisan db:seed`

### Development

When modifying files, you need to restart the docker instance to see the changes. This is because the files are loaded into Swooles memory and therefore cannot be modified after the server is started. You may be familiar with this workflow if you developed anything using Node.js.
