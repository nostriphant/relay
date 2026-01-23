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
    $logger = Mockery::mock(Psr\Log\LoggerInterface::class);
    $logger->shouldReceive('notice', 'debug', 'info', 'warning');
    
    $socket_file = sys_get_temp_dir() . '/relay.socket';
    $server = new nostriphant\Relay\Amp\WebsocketServer($socket_file, 1000, $logger);
    
    $relay = new \nostriphant\Relay\Relay($server,
        'Transpher Relay',
        'Some interesting description goes here',
        (string) nostriphant\NIP19\Bech32::npub('c0bb181bc39c4e59768805bbc5bdd34c508f14b01a298d63be4510d97417ce01'),
        'transpher@nostriphant.dev'
    );
    
    
    $engine = new \nostriphant\Stores\Engine\Disk(data_directory());
    $store = new \nostriphant\Stores\Store($engine, []);
    $blossom = new nostriphant\Relay\Blossom(files_directory());
    expect($socket_file)->not()->toBeFile();
    $stop = $relay($store, fn(callable $define) => $blossom($define));
   
    expect($socket_file)->toBeFile();
    
    $stop();
    
    unlink($socket_file);
});
