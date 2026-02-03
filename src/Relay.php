<?php

namespace nostriphant\Relay;

readonly class Relay {
    private MessageHandlerFactory $message_handler_factory;
    
    public function __construct(private \nostriphant\Relay\InformationDocument $information_document) {
    }
    
    public function __invoke(string $socket, int $max_connections_per_ip, \Psr\Log\LoggerInterface $log, callable $static_routes) : callable {
        return new Amp\WebsocketServer($socket, $max_connections_per_ip, $log, function(callable $define) use ($static_routes) {
            $define('GET', '/', function(callable $websocket, array $headers) {
                if (in_array ('application/nostr+json', $headers['accept'] ?? [])) {
                    return [
                        'headers' => ['Content-Type' => 'application/nostr+json'],
                        'body' => json_encode($this->information_document)
                    ];
                }
                return $websocket();
            });
        
            $static_routes($define);
        });
    }
}
