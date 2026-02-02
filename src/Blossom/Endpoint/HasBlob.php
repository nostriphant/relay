<?php

namespace nostriphant\Relay\Blossom\Endpoint;

use \nostriphant\Relay\Blossom\Endpoint;

readonly class HasBlob implements Endpoint {
    
    public function __construct(private string $path) {

    }
    
    #[\Override]
    public function __invoke(array $attributes): array {
        $blob = new \nostriphant\Relay\Blossom\Blob($this->path . DIRECTORY_SEPARATOR . $attributes['hash']);
        return match ($blob->exists) {
            true => [
                'headers' => [
                    'Content-Type' => $blob->type,
                    'Content-Length' => $blob->size
                ]
            ],
            false => ['code' => 404]
        };
    }
}
