<?php

namespace nostriphant\Relay\Blossom\File;

readonly class Regular implements \nostriphant\Relay\Blossom\Endpoint {
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
}
