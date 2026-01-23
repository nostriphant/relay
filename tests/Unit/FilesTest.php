<?php

use nostriphant\Relay\Files;
use function \nostriphant\RelayTests\files_directory;

beforeAll(function() {
    expect(\nostriphant\RelayTests\make_files_directory())->toBeTrue();
    expect(files_directory())->toBeDirectory();
});
afterAll(function() {
    \nostriphant\RelayTests\destroy_files_directory();
});

it('stores file, when event is in store', function () {
    $event_id = uniqid();

    $files = new Files(files_directory() . '/', fn() => false);
    $file = tempnam(sys_get_temp_dir(), 'file');
    file_put_contents($file, uniqid());
    $hash = hash_file('sha256', $file);

    $files($hash)($event_id, 'file://' . $file);
    expect(files_directory() . '/' . $hash)->toBeFile();
});
