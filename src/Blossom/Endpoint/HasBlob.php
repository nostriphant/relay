<?php

namespace nostriphant\Relay\Blossom\Endpoint;

use \nostriphant\Relay\Blossom\Endpoint;

readonly class HasBlob implements Endpoint {
    
    public function __construct(private string $path) {

    }
    
    #[\Override]
    public function __invoke(array $attributes): array {
        $path = $this->path . DIRECTORY_SEPARATOR . $attributes['hash'];
        return match (file_exists($path)) {
            true => [
                'headers' => [
                    'Content-Type' => 'text/plain',
                    'Content-Length' => filesize($this->path . DIRECTORY_SEPARATOR . $attributes['hash'])
                ]
            ],
            false => ['code' => 404]
        };
    }
}
