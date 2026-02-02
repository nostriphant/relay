<?php


namespace nostriphant\Relay\Blossom;

final readonly class Blob {
    
    public function __construct(public string $path) {
        
    }

    public function __get(string $name): mixed {
        return match($name) {
            'type' => 'text/plain',
            'size' => filesize($this->path),
            'exists' => file_exists($this->path)
        };
    }
    
    public function __invoke(): mixed {
        return file_get_contents($this->path);
    }
    
}
