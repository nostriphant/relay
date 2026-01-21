<?php

namespace nostriphant\Relay;

readonly class Blossom {

    const ROUTES = [
        'HEAD' => '/{hash:\w+}',
        'GET' => '/{hash:\w+}'
    ];

    public function __construct(private Files $files) {
        
    }

    public function __invoke(string $hash): array {
        $file = ($this->files)($hash);
        if ($file === null) {
            return [
                'code' => 404,
                'headers' => [
                    'Content-Type' => 'text/plain'
                ],
                'body' => ''
            ];
        }
        return [
            'headers' => [
                'Content-Type' => 'text/plain',
                'Content-Length' => filesize($file->path)
            ],
            'body' => $file()
        ];
    }
}
