<?php

namespace nostriphant\Relay\Blossom\Endpoint;

use \nostriphant\Relay\Blossom\Endpoint;

readonly class GetBlob implements Endpoint {
    
    public function __construct(private string $path) {

    }
    
    #[\Override]
    public function __invoke(array $attributes): array {
        $path = $this->path . DIRECTORY_SEPARATOR . $attributes['hash'];
        return [
            'headers' => [
                'Content-Type' => 'text/plain',
                'Content-Length' => filesize($path)
            ],
            'body' => file_get_contents($path)
        ];
    }
    
    static function fromPath(string $path) : Endpoint {
        return match (file_exists($path)) {
            true => new self($path),
            false => new File\Missing()
        };
    }
}
