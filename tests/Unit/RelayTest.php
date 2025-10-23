<?php


beforeAll(function() {
    assert(\nostriphant\RelayTests\make_files_directory() === true);
});
afterAll(function() {
    assert(\nostriphant\RelayTests\destroy_files_directory() === true);
});

it('can instanatiate Relay', function () {
    
    $engine = new \nostriphant\Stores\Engine\Disk(DATA_DIR);
    $store = new \nostriphant\Stores\Store($engine, []);
    $relay = new \nostriphant\Relay\Relay($store, FILES_DIR);

    $logger = Mockery::mock(Psr\Log\LoggerInterface::class);
    $logger->shouldReceive('notice', 'debug', 'info', 'warning');
    
    $socket_file = sys_get_temp_dir() . '/relay.socket';
    unlink($socket_file);
    
    expect($socket_file)->not()->toBeFile();
    $stop = $relay($socket_file, 1000, $logger);
   
    expect($socket_file)->toBeFile();
    
    $stop();
});


it('can boot a relay instance', function() {
    
    $socket = ROOT_DIR . "/relay.socket";
    
    expect($socket)->not()->toBeFile();
    
    $log_file = ROOT_DIR . "/logs/relay.log";
    
    $runtest = fn(string $line) => var_dump($line, 'Listening on http://' . $socket);
    $cwd = ROOT_DIR;
    $process_id = 'relay-' . substr(sha1($socket), 0, 6);
    $output_file = $cwd . "/logs/{$process_id}-output.log";
    $error_file = $cwd . "/logs/{$process_id}-errors.log";
    $descriptorspec = [
        0 => ["pipe", "r"],  
        1 => ["pipe", "w"],  
        2 => ["file", $error_file, "w"]
    ];

    $cmd = [PHP_BINARY, '-r', '    
    require_once __DIR__ . "/vendor/autoload.php";

    $logger = new Monolog\Logger("relay");

    $logger->pushHandler(new Monolog\Handler\StreamHandler("' . $log_file . '", "INFO"));
    $logger->pushHandler(new Monolog\Handler\StreamHandler(STDOUT, "INFO"));

    Monolog\ErrorHandler::register($logger);


    $store_path = __DIR__ . "/data/events";
    $events = new \nostriphant\Stores\Engine\Disk($store_path);
    $store = new nostriphant\Stores\Store($events, []);

    $relay = new \nostriphant\Relay\Relay($store, __DIR__ . "/data/files");

    $stop = $relay("'.$socket.'", 1000, $logger);

    new nostriphant\Relay\AwaitSignal(function(int $signal) use ($stop, $logger) {
        $logger->info(sprintf("Received signal %d, stopping Relay server", $signal));
        $stop();
    });'];
    $process = proc_open($cmd, $descriptorspec, $pipes, $cwd, []);
    
    fclose($pipes[0]);
    
    while ($line = fgets($pipes[1])) {
        if (str_contains($line, 'Listening on http://' . $socket)) {
            break;
        }
    }
    proc_terminate($process);
    proc_close($process);
    
    unlink($socket);
    
    expect(file_get_contents($error_file))->toBeEmpty();
    unlink($error_file);
    unlink($log_file);
});