<?php    
require_once dirname(__DIR__) . "/bootstrap.php";

$socket = $_SERVER['argv'][1];

$logger = new Monolog\Logger("relay");
$logger->pushHandler(new Monolog\Handler\StreamHandler(STDOUT, "INFO"));

Monolog\ErrorHandler::register($logger);


$store_path = \nostriphant\Relay\data_directory() . "/events";
$files_path = \nostriphant\Relay\data_directory() . "/files";
        
$events = new \nostriphant\Stores\Engine\Disk($store_path);
$store = new nostriphant\Stores\Store($events, []);

$relay = new \nostriphant\Relay\Relay(new \nostriphant\Relay\InformationDocument(
    name: "Transpher Relay",
    description: "Some interesting description goes here",
    pubkey: (new \nostriphant\NIP19\Bech32((string) nostriphant\NIP19\Bech32::npub("c0bb181bc39c4e59768805bbc5bdd34c508f14b01a298d63be4510d97417ce01")))(),
    contact: "transpher@nostriphant.dev",
    supported_nips: [1, 2, 9, 11, 12, 13, 16, 20, 22, 33, 45],
    software: "https://github.com/nostriphant/relay",
    version: "2.2.0"
));

$server = $relay($socket, 1000, $logger, new \nostriphant\Functional\FunctionList());

new nostriphant\Relay\AwaitSignal($server($store));