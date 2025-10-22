# relay
PHP relay, part of Transpher

```php
<?php

$log = new \Monolog\Logger($identifier);

$log->pushHandler(new \Monolog\Handler\StreamHandler(__DIR__ . '/logs/' . $identifier . '.log', \Monolog\Level::Info));
$log->pushHandler(new \Monolog\Handler\StreamHandler(STDOUT, \Monolog\Level::Info));

Monolog\ErrorHandler::register($log);

$engine = new \nostriphant\Stores\Engine\SQLite(new SQLite3(__DIR__ . '/data/transpher.sqlite'));
$store = new \nostriphant\Stores\Store($engine);

$relay = new \nostriphant\Relay($store, __DIR__ . '/data/files');

list($ip, $port) = explode(":", $_SERVER['argv'][1], 2);

$max_connections_per_ip = 1000;
$relay("127.0.0.1", "80", $connections, $log);

```