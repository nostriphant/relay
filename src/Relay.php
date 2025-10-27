<?php

namespace nostriphant\Relay;

use nostriphant\Stores\Store;

readonly class Relay {
    private Amp\WebsocketServer $server;
    
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
    
    public function __invoke(string $socket, int $max_connections_per_ip, \Psr\Log\LoggerInterface $log): callable {
        return ($this->server)($socket, $max_connections_per_ip, $log);
    }
    
    public static function software() : string {
        return json_decode(file_get_contents(dirname(__DIR__) . '/composer.json'))->description;
    }
    
    public static function version() : string {
        return file_get_contents(dirname(__DIR__) . '/VERSION');
    }
}
