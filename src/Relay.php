<?php

namespace nostriphant\Relay;

use nostriphant\Stores\Store;

readonly class Relay {
    private Amp\WebsocketServer $server;
    private InformationDocument $information_document;
    
    public function __construct(Store $events, Blossom $blossom, string $relay_name, string $relay_description, string $relay_owner_npub, $relay_contact, \Psr\Log\LoggerInterface $log) {
        $messageHandlerFactory =  new MessageHandlerFactory($events, $log);
        
        $this->information_document = new \nostriphant\Relay\InformationDocument(
                name: $relay_name,
                description: $relay_description,
                pubkey: (new \nostriphant\NIP19\Bech32($relay_owner_npub))(),
                contact: $relay_contact,
                supported_nips: \nostriphant\Relay\Relay::enabled_nips(),
                software: \nostriphant\Relay\Relay::software(),
                version: \nostriphant\Relay\Relay::version()
        );
        
        $this->server = new Amp\WebsocketServer($messageHandlerFactory, $log, fn(callable $define) => $blossom($define));
    }
    
    public function __invoke(string $socket, int $max_connections_per_ip): callable {
        return ($this->server)($socket, $max_connections_per_ip, $this->information_document);
    }
    
    public static function enabled_nips() : array {
        return [1, 2, 9, 11, 12, 13, 16, 20, 22, 33, 45];
    }
    
    public static function software() : string {
        return json_decode(file_get_contents(dirname(__DIR__) . '/composer.json'))->homepage;
    }
    
    public static function version() : string {
        return file_get_contents(dirname(__DIR__) . '/VERSION');
    }
}
