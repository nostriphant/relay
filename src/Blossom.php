<?php

namespace nostriphant\Relay;

readonly class Blossom {
    
    public function __construct(private string $path) {
       
    }

    public function __invoke(Routes $routes) : Routes {
        return $routes
            ->bind(new Blossom\Endpoint\HasBlob($this->path))
            ->bind(new Blossom\Endpoint\GetBlob($this->path));
    }
}
