<?php

namespace nostriphant\Relay;

readonly class Blossom {
    
    public function __construct(private string $path) {
       
    }

    public function __invoke(callable $define) : void {
        $define('HEAD', '/{hash:\w+}', fn(string $hash) => Blossom\Endpoint\HasBlob::fromPath($this->path . DIRECTORY_SEPARATOR . $hash)());
        $define('GET', '/{hash:\w+}', fn(string $hash) => Blossom\Endpoint\GetBlob::fromPath($this->path . DIRECTORY_SEPARATOR . $hash)());
    }
}
