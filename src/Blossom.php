<?php

namespace nostriphant\Relay;

readonly class Blossom {
    
    public function __construct(private string $path) {
       
    }

    public function __invoke(Routes $routes) : Routes {
        return $routes
            ->bind(fn($define) => $define('HEAD', '/{hash:\w+}', new Blossom\Endpoint\HasBlob($this->path)))
            ->bind(fn($define) => $define('GET', '/{hash:\w+}', new Blossom\Endpoint\GetBlob($this->path)));
    }
}
