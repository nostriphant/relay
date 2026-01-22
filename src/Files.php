<?php

namespace nostriphant\Relay;

readonly class Files {

    public function __construct(private string $path, private \Closure $event_missing) {
        foreach (glob($path . DIRECTORY_SEPARATOR . '*') as $file) {
            if (is_file($file) === false) {
                continue;
            }

            $events_path = self::makeEventsPath($file);
            if (is_dir($events_path) === false) {
                unlink($file);
                continue;
            }
            $event_files = glob($events_path . DIRECTORY_SEPARATOR . '*');
            if (count($event_files) === 0) {
                rmdir($events_path);
                unlink($file);
                continue;
            }

            foreach ($event_files as $event_file) {
                if (($this->event_missing)(basename($event_file))) {
                    unlink($event_file);
                }
            }

            if (count(glob($events_path . DIRECTORY_SEPARATOR . '*')) === 0) {
                rmdir($events_path);
                unlink($file);
                continue;
            }
        }
    }


    static function makeEventsPath(string $file_path) {
        return $file_path . '.events';
    }

    public function __invoke(string $hash): ?object {
        if (file_exists($this->path) === false) {
                    return null;
        }
        return new class($this->path . DIRECTORY_SEPARATOR . $hash, $this->event_missing) {

            public function __construct(public string $path, private \Closure $event_missing) {
                
            }

            public function __invoke(): ?string {
                if (func_num_args() === 0) {
                    return file_get_contents($this->path);
                }

                list($event_id, $remote_file) = func_get_args();
                if (($this->event_missing)($event_id)) {
                    return null;
                }

                $remote_handle = fopen($remote_file, 'r');
                $local_handle = fopen($this->path, 'w');
                while ($buffer = fread($remote_handle, 512)) {
                    fwrite($local_handle, $buffer);
                }
                fclose($remote_handle);
                fclose($local_handle);

                $events_path = Files::makeEventsPath($this->path);
                is_dir($events_path) || mkdir($events_path);
                touch($events_path . DIRECTORY_SEPARATOR . $event_id);
                return null;
            }
        };
    }
}
