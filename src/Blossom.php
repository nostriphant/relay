<?php

namespace nostriphant\Relay;

readonly class Blossom {
    
    public function __construct(private string $path) {
       
    }

    public function __invoke(callable $define) : void {
        $define(['HEAD', 'GET'], '/{hash:\w+}', function(string $hash) : array {
        
            $file_path = $this->path . DIRECTORY_SEPARATOR . $hash;
            if (Blossom\File::exists($file_path) === false) {
                return [
                    'code' => 404,
                    'headers' => [
                        'Content-Type' => 'text/plain'
                    ],
                    'body' => ''
                ];
            }
            
            $file = new Blossom\File($this->path . DIRECTORY_SEPARATOR . $hash);
            return [
                'headers' => [
                    'Content-Type' => 'text/plain',
                    'Content-Length' => filesize($file_path)
                ],
                'body' => $file()
            ];
        });
    }
}
