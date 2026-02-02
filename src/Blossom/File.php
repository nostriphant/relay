<?php

namespace nostriphant\Relay\Blossom;

class File {
    public function __construct(public string $path) {

    }
    
    static function exists(self $file) : bool {
        return file_exists($file->path);
    }
    
    static function size(self $file) : int {
        return filesize($file->path);
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
}
