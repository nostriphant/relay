<?php

namespace nostriphant\Relay\Blossom\Endpoint;

use \nostriphant\Relay\Blossom\Endpoint;

readonly class Blob implements Endpoint {
    
    public function __construct(private string $path) {

    }
    
    private function makeEndpoint(string $hash, callable $exists) {
        return (new \nostriphant\Relay\Blossom\Blob($this->path . DIRECTORY_SEPARATOR . $hash))(
            fn(\nostriphant\Relay\Blossom\Blob $blob) => array_merge([
                    'headers' => [
                        'Content-Type' => $blob->type,
                        'Content-Length' => $blob->size
                    ]
                ],$exists($blob)), 
            fn() => ['code' => 404]
        );
    }
    
    #[\Override]
    public function __invoke(callable $define) : void {
        $define('HEAD', '/{hash:\w+}', fn(array $attributes) => $this->makeEndpoint($attributes['hash'], fn(\nostriphant\Relay\Blossom\Blob $blob) => []));
        
        $define('GET', '/{hash:\w+}', fn(array $attributes) => $this->makeEndpoint($attributes['hash'], fn(\nostriphant\Relay\Blossom\Blob $blob) => [
            'body' => $blob->contents
        ]));
        
    }
}
