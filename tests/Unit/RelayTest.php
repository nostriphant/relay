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
    
    $engine = new \nostriphant\Stores\Engine\Disk(data_directory());
    $store = new \nostriphant\Stores\Store($engine, []);
    $relay = new \nostriphant\Relay\Relay(new \nostriphant\Relay\InformationDocument(
        name: "Transpher Relay",
        description: "Some interesting description goes here",
        pubkey: (new \nostriphant\NIP19\Bech32((string) nostriphant\NIP19\Bech32::npub("c0bb181bc39c4e59768805bbc5bdd34c508f14b01a298d63be4510d97417ce01")))(),
        contact: "transpher@nostriphant.dev",
        supported_nips: [1, 2, 9, 11, 12, 13, 16, 20, 22, 33, 45],
        software: "https://github.com/nostriphant/relay",
        version: "2.2.0"
    ));
    
    
    
    $socket_file = sys_get_temp_dir() . '/relay.socket';
    $blossom = new nostriphant\Relay\Blossom(files_directory());
    expect($socket_file)->not()->toBeFile();
    $server = $relay($socket_file, 1000, $logger, $blossom(new \nostriphant\Functional\FunctionList()));
    $stop = $server($store);
    expect($socket_file)->toBeFile();
    
    $stop();
    
    unlink($socket_file);
});
