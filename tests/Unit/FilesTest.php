<?php

use nostriphant\NIP01Tests\Functions as NIP01TestFunctions;
use nostriphant\Relay\Files;

beforeAll(function() {
    assert(\nostriphant\RelayTests\make_files_directory() === true);
});
afterAll(function() {
    assert(\nostriphant\RelayTests\destroy_files_directory() === true);
});

it('stores file, when event is in store', function () {
    $event_id = uniqid();

    $store = \Pest\store();
    $store[$event_id] = NIP01TestFunctions::event(['id' => $event_id = uniqid()]);
    expect(isset($store[$event_id]))->toBeTrue();

    $files = new Files(FILES_DIR . '/', $store);
    $file = tempnam(sys_get_temp_dir(), 'file');
    file_put_contents($file, uniqid());
    $hash = hash_file('sha256', $file);

    $files($hash)($event_id, 'file://' . $file);
    expect(FILES_DIR . '/' . $hash)->toBeFile();
    expect(FILES_DIR . '/' . $hash . '.events')->toBeDirectory();
    expect(FILES_DIR . '/' . $hash . '.events/' . $event_id)->toBeFile();
});


it('ignores file, when event is NOT in store', function () {
    $event_id = uniqid();

    $store = \Pest\store();

    $files = new Files(FILES_DIR . '/', $store);
    $file = tempnam(sys_get_temp_dir(), 'file');
    file_put_contents($file, uniqid());
    $hash = hash_file('sha256', $file);

    $files($hash)($event_id, 'file://' . $file);
    expect(FILES_DIR . '/' . $hash)->not()->toBeFile();
    expect(FILES_DIR . '/' . $hash . '.events/' . $event_id)->not()->toBeFile();
});


it('removes files, when no events directory', function () {
    $store = \Pest\store();

    $hash = uniqid();

    expect(FILES_DIR . '/' . $hash . '.events')->not()->toBeDirectory();

    file_put_contents(FILES_DIR . '/' . $hash, uniqid());
    mkdir(FILES_DIR . '/' . $hash . '.events');
    expect(FILES_DIR . '/' . $hash)->toBeFile();
    expect(FILES_DIR . '/' . $hash . '.events')->toBeDirectory();

    $files = new Files(FILES_DIR . '/', $store);

    expect(FILES_DIR . '/' . $hash)->not()->toBeFile();
    expect(FILES_DIR . '/' . $hash . '.events')->not()->toBeDirectory();
});

it('removes files, when no events in events directory exist', function () {
    $store = \Pest\store();

    $hash = uniqid();
    file_put_contents(FILES_DIR . '/' . $hash, uniqid());
    expect(FILES_DIR . '/' . $hash)->toBeFile();
    expect(FILES_DIR . '/' . $hash . '.events')->not()->toBeDirectory();

    $files = new Files(FILES_DIR . '/', $store);

    expect(FILES_DIR . '/' . $hash)->not()->toBeFile();
});

it('removes files, when event is NOT in store', function () {
    $event_id = uniqid();

    $store = \Pest\store();

    $hash = uniqid();
    file_put_contents(FILES_DIR . '/' . $hash, uniqid());
    mkdir(FILES_DIR . '/' . $hash . '.events');
    touch(FILES_DIR . '/' . $hash . '.events/' . $event_id);
    expect(FILES_DIR . '/' . $hash)->toBeFile();
    expect(FILES_DIR . '/' . $hash . '.events')->toBeDirectory();
    expect(FILES_DIR . '/' . $hash . '.events/' . $event_id)->toBeFile();

    $files = new Files(FILES_DIR . '/', $store);

    expect(FILES_DIR . '/' . $hash . '.events/' . $event_id)->not()->toBeFile();
    expect(FILES_DIR . '/' . $hash)->not()->toBeFile();
});
