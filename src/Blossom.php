<?php

namespace nostriphant\Relay;

readonly class Blossom {
    
    public function __construct(private string $path) {
       
    }

    public function __invoke(Routes $routes) : Routes {
        return $routes
            ->bind(new Blossom\Endpoint\Blob($this->path));
    }
}
