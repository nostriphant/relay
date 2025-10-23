<?php


beforeAll(function() {
    assert(\nostriphant\RelayTests\make_files_directory() === true);
});
afterAll(function() {
    assert(\nostriphant\RelayTests\destroy_files_directory() === true);
});

it('stores file, when event is in store', function () {
    
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
