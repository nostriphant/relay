<?php

namespace nostriphant\Relay\Blossom\Endpoint;

use \nostriphant\Relay\Blossom\Endpoint;

readonly class Blob implements Endpoint {
    
    public function __construct(private string $path) {

    }
    
    static function makeEndpoint(string $path, callable $exists) {
        return (new \nostriphant\Relay\Blossom\Blob($path))(
            $exists, 
            fn() => ['code' => 404]
        );
    }
    
    #[\Override]
    public function __invoke(callable $define) : void {
        $define('GET', '/{hash:\w+}', fn(array $attributes) => self::makeEndpoint($this->path . DIRECTORY_SEPARATOR . $attributes['hash'], fn(\nostriphant\Relay\Blossom\Blob $blob) => [
            'headers' => [
                'Content-Type' => $blob->type,
                'Content-Length' => $blob->size
            ],
            'body' => $blob->contents
        ]));
        
        $define('HEAD', '/{hash:\w+}', fn(array $attributes) => self::makeEndpoint($this->path . DIRECTORY_SEPARATOR . $attributes['hash'], fn(\nostriphant\Relay\Blossom\Blob $blob) => [
            'headers' => [
                'Content-Type' => $blob->type,
                'Content-Length' => $blob->size
            ]
        ]));
    }
}
