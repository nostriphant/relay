<?php

namespace nostriphant\Relay\Blossom;

readonly class File implements Endpoint {
    
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
    
    static function fromPath(string $path) : callable {
        return fn(string $hash) => (match (file_exists($path . DIRECTORY_SEPARATOR . $hash)) {
            true => new self($path . DIRECTORY_SEPARATOR . $hash),
            false => new File\Missing()
        })();
    }
}
