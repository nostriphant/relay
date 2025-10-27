<?php

namespace nostriphant\Relay;

use nostriphant\Stores\Store;

readonly class Relay {
    private Amp\WebsocketServer $server;
    private InformationDocument $information_document;
    
    public function __construct(Store $events, string $files_path, string $relay_name, string $relay_description, string $relay_owner_npub, $relay_contact) {
        $files = new Files($files_path, $events);
        $messageHandlerFactory =  new MessageHandlerFactory($events, $files);
        
        $this->information_document = new \nostriphant\Relay\InformationDocument(
                name: $relay_name,
                description: $relay_description,
                pubkey: (new \nostriphant\NIP19\Bech32($relay_owner_npub))(),
                contact: $relay_contact,
                supported_nips: \nostriphant\Relay\Relay::enabled_nips(),
                software: \nostriphant\Relay\Relay::software(),
                version: \nostriphant\Relay\Relay::version()
        );
        
        $blossom = new Blossom($files);
        $this->server = new Amp\WebsocketServer($messageHandlerFactory, function(callable $define) use ($blossom) : void {
            foreach (Blossom::ROUTES as $method => $route) {
                $define($method, $route, $blossom);
            }
        });
    }
    
    public function __invoke(string $socket, int $max_connections_per_ip, \Psr\Log\LoggerInterface $log): callable {
        return ($this->server)($socket, $max_connections_per_ip, $this->information_document, $log);
    }
    
    public static function enabled_nips() : array {
        return [1, 2, 9, 11, 12, 13, 16, 20, 22, 33, 45, 92, 94];
    }
    
    public static function software() : string {
        return json_decode(file_get_contents(dirname(__DIR__) . '/composer.json'))->description;
    }
    
    public static function version() : string {
        return file_get_contents(dirname(__DIR__) . '/VERSION');
    }
}
