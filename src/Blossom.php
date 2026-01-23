<?php

namespace nostriphant\Relay;

readonly class Blossom {
    
    private Files $files;

    const ROUTES = [
        'HEAD' => '/{hash:\w+}',
        'GET' => '/{hash:\w+}'
    ];

    public function __construct(private string $path) {
       
    }
    
    private function file(string $hash) {
        
        if (file_exists($this->path) === false) {
                    return null;
        }
        return new class($this->path . DIRECTORY_SEPARATOR . $hash) {

            public function __construct(public string $path) {
                
            }

            public function __invoke(): ?string {
                if (func_num_args() === 0) {
                    return file_get_contents($this->path);
                }

                list($event_id, $remote_file) = func_get_args();

                $remote_handle = fopen($remote_file, 'r');
                $local_handle = fopen($this->path, 'w');
                while ($buffer = fread($remote_handle, 512)) {
                    fwrite($local_handle, $buffer);
                }
                fclose($remote_handle);
                fclose($local_handle);
                return null;
            }
        };
    }

    public function __invoke(callable $define) : void {
        foreach (self::ROUTES as $method => $route) {
            $define($method, $route, function(string $hash) : array {
                $file = $this->file($hash);
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
            });
        }
    }
}
