<?php

namespace nostriphant\Relay;

use \nostriphant\Functional\FunctionList;

readonly class Blossom {
    
    public function __construct(private string $path) {
       
    }

    public function __invoke(FunctionList $routes) : FunctionList {
        return $routes
            ->bind(new Blossom\Endpoint\Blob($this->path));
    }
}
