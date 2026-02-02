<?php

namespace nostriphant\Relay\Blossom\Endpoint;

use \nostriphant\Relay\Blossom\Endpoint;

readonly class GetBlob implements Endpoint {
    
    public function __construct(private string $path) {

    }
    
    #[\Override]
    public function __invoke(): array {
        return [
            'headers' => [
                'Content-Type' => 'text/plain',
                'Content-Length' => filesize($this->path)
            ],
            'body' => file_get_contents($this->path)
        ];
    }
    
    static function fromPath(string $path) : Endpoint {
        return match (file_exists($path)) {
            true => new self($path),
            false => new File\Missing()
        };
    }
}
