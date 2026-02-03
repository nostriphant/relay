<?php

namespace nostriphant\Relay\Blossom\Endpoint;

use \nostriphant\Relay\Blossom\Endpoint;

readonly class GetBlob implements Endpoint {
    
    public function __construct(private string $path) {

    }
    
    #[\Override]
    public function __invoke(callable $define) : void {
        $define('GET', '/{hash:\w+}', function(array $attributes): array {
            return (new \nostriphant\Relay\Blossom\Blob($this->path . DIRECTORY_SEPARATOR . $attributes['hash']))(
                fn(\nostriphant\Relay\Blossom\Blob $blob) => [
                    'headers' => [
                        'Content-Type' => $blob->type,
                        'Content-Length' => $blob->size
                    ],
                    'body' => $blob->contents
                ], 
                fn() => ['code' => 404]
            );
        });
    }
}
