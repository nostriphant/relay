<?php

namespace nostriphant\Relay;

readonly class Blossom {
    
    public function __construct(private string $path) {
       
    }

    public function __invoke(callable $define) : void {
        $define('HEAD', '/{hash:\w+}', Blossom\Endpoint\HasBlob::fromPath($this->path));
        $define('GET', '/{hash:\w+}', Blossom\Endpoint\GetBlob::fromPath($this->path));
    }
}
