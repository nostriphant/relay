<?php

namespace nostriphant\Relay\Blossom\Endpoint;

use \nostriphant\Relay\Blossom\Endpoint;

readonly class GetBlob implements Endpoint {
    
    public function __construct(private string $path) {

    }
    
    #[\Override]
    public function __invoke(array $attributes): array {
        $path = $this->path . DIRECTORY_SEPARATOR . $attributes['hash'];
        return match (file_exists($path)) {
            true => [
                'headers' => [
                    'Content-Type' => 'text/plain',
                    'Content-Length' => filesize($path)
                ],
                'body' => file_get_contents($path)
            ],
            false => ['code' => 404]
        };
    }
    
    static function fromPath(string $path) : Endpoint {
        return match (file_exists($path)) {
            true => new self($path),
            false => new File\Missing()
        };
    }
}
