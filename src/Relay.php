<?php

namespace nostriphant\Relay;

use \nostriphant\Functional\FunctionList;

readonly class Relay {
    private MessageHandlerFactory $message_handler_factory;
    
    public function __construct(private \nostriphant\Relay\InformationDocument $information_document) {
    }
    
    public function __invoke(string $socket, int $max_connections_per_ip, \Psr\Log\LoggerInterface $log) : callable {
        $nip11_route = fn(callable $define) => $define('GET', '/', function(callable $websocket, array $headers) {
            if (in_array ('application/nostr+json', $headers['accept'] ?? [])) {
                return new \Amp\Http\Server\Response(...[
                    'headers' => ['Content-Type' => 'application/nostr+json'],
                    'body' => json_encode($this->information_document)
                ]);
            }
            return $websocket();
        });
        
        return new Amp\WebsocketServer($socket, $max_connections_per_ip, $log, new \ArrayIterator([$nip11_route]));
    }
}
