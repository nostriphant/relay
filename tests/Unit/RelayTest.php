<?php

use function \nostriphant\RelayTests\files_directory;
use function \nostriphant\Relay\data_directory;

beforeAll(function() {
    expect(\nostriphant\RelayTests\make_files_directory())->toBeTrue();
    expect(files_directory())->toBeDirectory();
});
afterAll(function() {
    \nostriphant\RelayTests\destroy_files_directory();
});

it('can instanatiate Relay', function () {
    
    $engine = new \nostriphant\Stores\Engine\Disk(data_directory());
    $store = new \nostriphant\Stores\Store($engine, []);
    $relay = new \nostriphant\Relay\Relay($store, files_directory(),
        'Transpher Relay',
        'Some interesting description goes here',
        (string) nostriphant\NIP19\Bech32::npub('c0bb181bc39c4e59768805bbc5bdd34c508f14b01a298d63be4510d97417ce01'),
        'transpher@nostriphant.dev'
    );

    $logger = Mockery::mock(Psr\Log\LoggerInterface::class);
    $logger->shouldReceive('notice', 'debug', 'info', 'warning');
    
    $socket_file = sys_get_temp_dir() . '/relay.socket';
    
    expect($socket_file)->not()->toBeFile();
    $stop = $relay($socket_file, 1000, $logger);
   
    expect($socket_file)->toBeFile();
    
    $stop();
    
    unlink($socket_file);
});


it('can boot a relay instance', function() {
    
    $socket = '127.0.0.1:8087';
    
    $cwd = ROOT_DIR;
    $log_directory = $cwd . "/logs";
    is_dir($log_directory) || mkdir($log_directory);
    
    $log_file = $log_directory . "/relay.log";
    
    $process_id = 'relay-' . substr(sha1($socket), 0, 6);
    $error_file = $log_directory . "/{$process_id}-errors.log";
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

    $relay = new \nostriphant\Relay\Relay($store, __DIR__ . "/data/files",
        "Transpher Relay",
        "Some interesting description goes here",
        (string) nostriphant\NIP19\Bech32::npub("c0bb181bc39c4e59768805bbc5bdd34c508f14b01a298d63be4510d97417ce01"),
        "transpher@nostriphant.dev"
    );

    $stop = $relay("'.$socket.'", 1000, $logger);

    new nostriphant\Relay\AwaitSignal($stop);'];
    $process = proc_open($cmd, $descriptorspec, $pipes, $cwd, []);
    
    expect($process)->toBeResource(file_get_contents($error_file));
    
    fclose($pipes[0]);
    
    while ($line = fgets($pipes[1])) {
        if (str_contains($line, 'Listening on http://' . $socket)) {
            break;
        }
    }
    
    $expected_body = json_encode([
            'name' => 'Transpher Relay',
            'description' => 'Some interesting description goes here',
            'pubkey' => 'c0bb181bc39c4e59768805bbc5bdd34c508f14b01a298d63be4510d97417ce01',
            'contact' =>'transpher@nostriphant.dev',
            'supported_nips' => \nostriphant\Relay\Relay::enabled_nips(),
            'software' => \nostriphant\Relay\Relay::software(),
            'version' => \nostriphant\Relay\Relay::version()
    ]);
    
    $body = file_get_contents('http://' . $socket . '/');
    expect($body)->toBeJson();
    expect($body)->tobe($expected_body);
    
    proc_terminate($process);
    sleep(1);
    
    proc_close($process);
    
    expect(file_get_contents($error_file))->toBeEmpty();
    unlink($error_file);
    unlink($log_file);
});