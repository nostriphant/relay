<?php

namespace nostriphant\Relay\Blossom\Endpoint;

use nostriphant\Relay\Blossom\Endpoint;

readonly class Blob implements Endpoint {
    
    public function __construct(private string $method, private string $path) {

    }
    
    #[\Override]
    public function __invoke(callable $define) : void {
        $define($this->method, '/{hash:\w+}', fn(array $attributes) => (new \nostriphant\Relay\Blossom\Blob($this->path . DIRECTORY_SEPARATOR . $attributes['hash']))(
            function(\nostriphant\Relay\Blossom\Blob $blob) {
                $response = [
                    'headers' => [
                        'Content-Type' => $blob->type,
                        'Content-Length' => $blob->size
                    ]
                ];
                
                if ($this->method === 'GET') {
                    $response['body'] = $blob->contents;
                }
            
                return $response;
            }, 
            fn() => ['code' => 404]
        ));
    }
}
