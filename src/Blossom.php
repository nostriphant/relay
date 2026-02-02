<?php

namespace nostriphant\Relay;

readonly class Blossom {
    
    public function __construct(private string $path) {
       
    }

    public function __invoke(callable $define) : void {
        $define(['HEAD', 'GET'], '/{hash:\w+}', Blossom\File::fromPath($this->path));
    }
}
