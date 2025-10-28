<?php

use nostriphant\NIP01Tests\Functions as NIP01TestFunctions;
use nostriphant\Relay\Files;
use function \nostriphant\RelayTests\files_directory;
use function \nostriphant\Relay\data_directory;

beforeAll(function() {
    \nostriphant\Relay\make_data_directory();
    expect(data_directory())->toBeDirectory();
    assert(\nostriphant\RelayTests\make_files_directory() === true);
    expect(files_directory())->toBeDirectory();
});
afterAll(function() {
    assert(\nostriphant\RelayTests\destroy_files_directory() === true);
});

it('stores file, when event is in store', function () {
    $event_id = uniqid();

    $store = \Pest\store();
    $store[$event_id] = NIP01TestFunctions::event(['id' => $event_id = uniqid()]);
    expect(isset($store[$event_id]))->toBeTrue();

    $files = new Files(files_directory() . '/', $store);
    $file = tempnam(sys_get_temp_dir(), 'file');
    file_put_contents($file, uniqid());
    $hash = hash_file('sha256', $file);

    $files($hash)($event_id, 'file://' . $file);
    expect(files_directory() . '/' . $hash)->toBeFile();
    expect(files_directory() . '/' . $hash . '.events')->toBeDirectory();
    expect(files_directory() . '/' . $hash . '.events/' . $event_id)->toBeFile();
});


it('ignores file, when event is NOT in store', function () {
    $event_id = uniqid();

    $store = \Pest\store();

    $files = new Files(files_directory() . '/', $store);
    $file = tempnam(sys_get_temp_dir(), 'file');
    file_put_contents($file, uniqid());
    $hash = hash_file('sha256', $file);

    $files($hash)($event_id, 'file://' . $file);
    expect(files_directory() . '/' . $hash)->not()->toBeFile();
    expect(files_directory() . '/' . $hash . '.events/' . $event_id)->not()->toBeFile();
});


it('removes files, when no events directory', function () {
    $store = \Pest\store();

    $hash = uniqid();

    expect(files_directory() . '/' . $hash . '.events')->not()->toBeDirectory();

    file_put_contents(files_directory() . '/' . $hash, uniqid());
    mkdir(files_directory() . '/' . $hash . '.events');
    expect(files_directory() . '/' . $hash)->toBeFile();
    expect(files_directory() . '/' . $hash . '.events')->toBeDirectory();

    $files = new Files(files_directory() . '/', $store);

    expect(files_directory() . '/' . $hash)->not()->toBeFile();
    expect(files_directory() . '/' . $hash . '.events')->not()->toBeDirectory();
});

it('removes files, when no events in events directory exist', function () {
    $store = \Pest\store();

    $hash = uniqid();
    file_put_contents(files_directory() . '/' . $hash, uniqid());
    expect(files_directory() . '/' . $hash)->toBeFile();
    expect(files_directory() . '/' . $hash . '.events')->not()->toBeDirectory();

    $files = new Files(files_directory() . '/', $store);

    expect(files_directory() . '/' . $hash)->not()->toBeFile();
});

it('removes files, when event is NOT in store', function () {
    $event_id = uniqid();

    $store = \Pest\store();

    $hash = uniqid();
    file_put_contents(files_directory() . '/' . $hash, uniqid());
    mkdir(files_directory() . '/' . $hash . '.events');
    touch(files_directory() . '/' . $hash . '.events/' . $event_id);
    expect(files_directory() . '/' . $hash)->toBeFile();
    expect(files_directory() . '/' . $hash . '.events')->toBeDirectory();
    expect(files_directory() . '/' . $hash . '.events/' . $event_id)->toBeFile();

    $files = new Files(files_directory() . '/', $store);

    expect(files_directory() . '/' . $hash . '.events/' . $event_id)->not()->toBeFile();
    expect(files_directory() . '/' . $hash)->not()->toBeFile();
});
