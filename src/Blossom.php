<?php

namespace nostriphant\Relay;

readonly class Blossom {
    
    public function __construct(private string $path) {
       
    }

    public function __invoke(callable $define) : void {
        $define(['HEAD', 'GET'], '/{hash:\w+}', function(string $hash) : array {
            $file = new Blossom\File($this->path . DIRECTORY_SEPARATOR . $hash);
            if (Blossom\File::exists($file) === false) {
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
                    'Content-Length' => Blossom\File::size($file)
                ],
                'body' => $file()
            ];
        });
    }
}
