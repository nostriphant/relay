<?php

namespace nostriphant\Relay\Blossom\File;

readonly class Missing implements \nostriphant\Relay\Blossom\Endpoint {
    public function __construct() {

    }
    
    #[\Override]
    public function __invoke(): array {
        return [
            'code' => 404,
            'headers' => [
                'Content-Type' => 'text/plain'
            ],
            'body' => ''
        ];
    }
}
