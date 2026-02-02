<?php

namespace nostriphant\Relay;

readonly class Blossom {
    
    public function __construct(private string $path) {
       
    }

    public function __invoke(callable $define) : void {
        $define('HEAD', '/{hash:\w+}', new Blossom\Endpoint\HasBlob($this->path));
        $define('GET', '/{hash:\w+}', new Blossom\Endpoint\GetBlob($this->path));
    }
}
