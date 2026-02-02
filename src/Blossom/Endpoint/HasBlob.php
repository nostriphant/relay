<?php

namespace nostriphant\Relay\Blossom\Endpoint;

use \nostriphant\Relay\Blossom\Endpoint;

readonly class HasBlob implements Endpoint {
    
    public function __construct(private string $path) {

    }
    
    #[\Override]
    public function __invoke(array $attributes): array {
        return [
            'headers' => [
                'Content-Type' => 'text/plain',
                'Content-Length' => filesize($this->path . DIRECTORY_SEPARATOR . $attributes['hash'])
            ]
        ];
    }
    
    static function fromPath(string $path) : Endpoint {
        return match (file_exists($path)) {
            true => new self($path),
            false => new File\Missing()
        };
    }
}
