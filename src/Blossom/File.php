<?php

namespace nostriphant\Relay\Blossom;

class File {
    
    static function fromPath(string $path) : Endpoint {
        return match (file_exists($path)) {
            true => new File\Regular($path),
            false => new File\Missing()
        };
    }
}
