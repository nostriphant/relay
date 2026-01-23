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
$blossom = new nostriphant\Relay\Blossom($files_path);
$server = new nostriphant\Relay\Amp\WebsocketServer($socket, 1000, $logger);

$relay = new \nostriphant\Relay\Relay($server,
    "Transpher Relay",
    "Some interesting description goes here",
    (string) nostriphant\NIP19\Bech32::npub("c0bb181bc39c4e59768805bbc5bdd34c508f14b01a298d63be4510d97417ce01"),
    "transpher@nostriphant.dev",
);

$stop = $relay($store, fn(callable $define) => $blossom($define));

new nostriphant\Relay\AwaitSignal($stop);