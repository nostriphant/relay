<?php

namespace nostriphant\Relay\Blossom\Endpoint;

use \nostriphant\Relay\Blossom\Endpoint;

readonly class Blob implements Endpoint {
    
    public function __construct(private string $path) {

    }
    
    #[\Override]
    public function __invoke(callable $define) : void {
        $define('GET', '/{hash:\w+}', fn(array $attributes) => (new \nostriphant\Relay\Blossom\Blob($this->path . DIRECTORY_SEPARATOR . $attributes['hash']))(
            fn(\nostriphant\Relay\Blossom\Blob $blob) => [
                'headers' => [
                    'Content-Type' => $blob->type,
                    'Content-Length' => $blob->size
                ],
                'body' => $blob->contents
            ], 
            fn() => ['code' => 404]
        ));
        
        $define('HEAD', '/{hash:\w+}', fn(array $attributes) => (new \nostriphant\Relay\Blossom\Blob($this->path . DIRECTORY_SEPARATOR . $attributes['hash']))(
            fn(\nostriphant\Relay\Blossom\Blob $blob) => [
                'headers' => [
                    'Content-Type' => $blob->type,
                    'Content-Length' => $blob->size
                ]
            ], 
            fn() => ['code' => 404]
        ));
    }
}
