<?php

namespace nostriphant\Relay;

use nostriphant\Stores\Store;

class Relay {
    public function __construct(Store $events, string $files_path) {
        $files = new Files($files_path, $events);
        $messageHandlerFactory =  new MessageHandlerFactory($events, $files);
        
        
        $blossom = new Blossom($files);
        $this->server = new Amp\WebsocketServer($messageHandlerFactory, function(callable $define) use ($blossom) : void {
            foreach (Blossom::ROUTES as $method => $route) {
                $define($method, $route, $blossom);
            }
        });
    }
    
    public function __invoke(string $ip, string $port, int $max_connections_per_ip, \Psr\Log\LoggerInterface $log): void {
        ($this->server)($ip, $port, $max_connections_per_ip, $log, fn(int $signal) => $log->info(sprintf("Received signal %d, stopping Relay server", $signal)));
    }
}
