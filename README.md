# Nostriphant Relay
PHP relay

```php
<?php

$socket = '127.0.0.1:8080';
$whitelist = ['<hex_pubkey>'];

$log = new \Monolog\Logger($identifier);

$log->pushHandler(new \Monolog\Handler\StreamHandler(__DIR__ . '/logs/' . $identifier . '.log', \Monolog\Level::Info));
$log->pushHandler(new \Monolog\Handler\StreamHandler(STDOUT, \Monolog\Level::Info));

Monolog\ErrorHandler::register($log);

$relay = new \nostriphant\Relay\Relay(new \nostriphant\Relay\InformationDocument(
    name: $_SERVER['RELAY_NAME'],
    description: $_SERVER['RELAY_DESCRIPTION'],
    pubkey: (new \nostriphant\NIP19\Bech32($_SERVER['RELAY_OWNER_NPUB']))(),
    contact: $_SERVER['RELAY_CONTACT'],
    supported_nips: [1, 2, 9, 11, 12, 13, 16, 20, 22, 33, 45],
    software: json_decode(file_get_contents(__DIR__ . '/composer.json'))->homepage,
    version: trim(file_get_contents(__DIR__ . '/VERSION'))
));

$server = $relay($socket, $_SERVER['RELAY_MAX_CONNECTIONS_PER_IP'] ?? 1000, $logger);

$logger->info('Loading store ' . (!empty($whitelist) ? ' with whitelist' : '')  . '.');
$store = new nostriphant\Stores\Store($events, $whitelist);

$logger->debug('Starting relay.');
$stop = $server($store);

new nostriphant\Relay\AwaitSignal(function(int $signal) use ($stop, $logger) {
    $logger->info(sprintf("Received signal %d, stopping Relay server", $signal));
    $stop();
});

```
