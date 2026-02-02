<?php

namespace nostriphant\Relay;

readonly class Relay {
    private MessageHandlerFactory $message_handler_factory;
    
    public function __construct(private string $name, private string $description, private string $owner_npub, private string $contact) {
    }
    
    public function __invoke(string $socket, int $max_connections_per_ip, \Psr\Log\LoggerInterface $log, callable $static_routes) : callable {
        return new Amp\WebsocketServer($socket, $max_connections_per_ip, $log, function(callable $define) use ($static_routes) {
            $define('GET', '/', function(callable $websocket, array $headers) {
                if (in_array ('application/nostr+json', $headers['accept'] ?? [])) {
                    return [
                        'headers' => ['Content-Type' => 'application/nostr+json'],
                        'body' => json_encode(new \nostriphant\Relay\InformationDocument(
                                name: $this->name,
                                description: $this->description,
                                pubkey: (new \nostriphant\NIP19\Bech32($this->owner_npub))(),
                                contact: $this->contact,
                                supported_nips: \nostriphant\Relay\Relay::enabled_nips(),
                                software: \nostriphant\Relay\Relay::software(),
                                version: \nostriphant\Relay\Relay::version()
                        ))
                    ];
                }
                return $websocket();
            });
        
            $static_routes($define);
        });
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
