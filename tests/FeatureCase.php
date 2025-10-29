<?php

namespace nostriphant\RelayTests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class FeatureCase extends BaseTestCase
{
    const SOCKET = '127.0.0.1:8087';
    const RELAY_URL = 'http://' . self::SOCKET;
    const LOG_DIRECTORY = ROOT_DIR . "/logs";
    const LOG_OUTPUT = self::LOG_DIRECTORY . "/relay.log";
    const LOG_ERRORS = self::LOG_DIRECTORY . "/relay-errors.log";
    
    static private $process;
    
    static function relay_output() {
        return file_get_contents(self::LOG_OUTPUT);
    }
    static function relay_errors() {
        return file_get_contents(self::LOG_ERRORS);
    }
    static function relay_process() {
        if (isset(self::$process) === false) {
            is_dir(self::LOG_DIRECTORY) || mkdir(self::LOG_DIRECTORY);
            
            $descriptorspec = [
                0 => ["pipe", "r"],  
                1 => ["file", self::LOG_OUTPUT, "w"], 
                2 => ["file", self::LOG_ERRORS, "w"]
            ];
            self::$process = proc_open([PHP_BINARY, '-f',  './tests/relay.php', self::SOCKET], $descriptorspec, $pipes, ROOT_DIR, []);

            expect(self::$process)->toBeResource(self::relay_errors());
            fclose($pipes[0]);

            while (str_contains(self::relay_output(), 'Listening on ' . self::RELAY_URL) === false){}
        }

        return self::$process;
    }
    
    static function end_relay_process() {
        proc_terminate(self::$process);
        sleep(1);

        proc_close(self::$process);

        expect(self::relay_errors())->toBeEmpty();
        unlink(self::LOG_ERRORS);
        unlink(self::LOG_OUTPUT);
    }
    
    public function expectRelayResponse(string $path, int $code, string $content_type, string $method = 'GET') {
        $curl = curl_init(self::RELAY_URL . $path);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_NOBODY, $method === 'HEAD');
        $body = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);
        expect($info['http_code'])->toBe($code);
        expect($info['content_type'])->toContain($content_type);

        return $body;
    }
    
    public function writeFile(string $content) {
        $hash = hash('sha256', $content);
        file_put_contents(files_directory() . DIRECTORY_SEPARATOR . $hash, $content);
        expect(files_directory() . DIRECTORY_SEPARATOR . $hash)->toBeFile();
        expect(file_get_contents(files_directory() . DIRECTORY_SEPARATOR . $hash))->toBe($content);
        return $hash;
    }
}
