<?php

namespace nostriphant\Relay\Blossom\Endpoint;

use nostriphant\Relay\Blossom\Endpoint;

readonly class Blob implements Endpoint {
    
    public function __construct(private string $method, private string $path) {

    }
    
    #[\Override]
    public function __invoke(callable $define) : array {
        return $define($this->method, '/{hash:\w+}', fn(array $attributes) => (new \nostriphant\Relay\Blossom\Blob($this->path . DIRECTORY_SEPARATOR . $attributes['hash']))(
            fn(\nostriphant\Relay\Blossom\Blob $blob) => array_merge([
                    'headers' => [
                        'Content-Type' => $blob->type,
                        'Content-Length' => $blob->size
                    ]
                ], $this->method === 'HEAD' ? [] : ['body' => $blob->contents]), 
            fn() => ['code' => 404]
        ));
    }
}
