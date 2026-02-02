<?php

namespace nostriphant\Relay\Blossom;

class File {
    public function __construct(public string $path) {

    }
    
    static function fromPath(string $path) : self {
        return new self($path);
    }

    public function __invoke(): array {
        if (file_exists($this->path) === false) {
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
                'Content-Length' => filesize($this->path)
            ],
            'body' => file_get_contents($this->path)
        ];
    }
}
