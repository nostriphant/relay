<?php

use nostriphant\NIP01Tests\Functions as NIP01TestFunctions;
use nostriphant\RelayTests\Factory;
use function Pest\incoming;

beforeAll(function() {
    assert(\nostriphant\RelayTests\make_files_directory() === true);
});
afterAll(function() {
    assert(\nostriphant\RelayTests\destroy_files_directory() === true);
});

it('downloads NIP-92 files (kind 1, with imeta tag) into a data folder', function () {
    $file = tempnam(sys_get_temp_dir(), 'file');
    file_put_contents($file, 'downloads NIP-92 files (kind 1, with imeta tag) into a data folder');
    $hash = hash_file('sha256', $file);

    expect(FILES_DIR . '/' . $hash)->not()->toBeFile();
    expect(FILES_DIR . '/' . $hash . '.events')->not()->toBeDirectory();

    $sender_key = NIP01TestFunctions::key_sender();
    $message = Factory::event($sender_key, 1, 'Note with a reference to file://' . $file,
            ['imeta',
                'url file://' . $file,
                'm text/plain',
                'x ' . $hash
            ]
    );

    $store = \Pest\store();

    expect(\Pest\handle($message, incoming($store)))->toHaveReceived(
            ['OK', $message()[1]['id'], true]
    );

    expect(isset($store[$message()[1]['id']]))->toBeTrue();

    expect(FILES_DIR . '/' . $hash)->toBeFile();
    expect(FILES_DIR . '/' . $hash . '.events')->toBeDirectory();
    expect(FILES_DIR . '/' . $hash . '.events/' . $message()[1]['id'])->toBeFile();
});
