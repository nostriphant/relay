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
    expect(files_directory() . '/' . $hash . '.events')->toBeDirectory();
    expect(files_directory() . '/' . $hash . '.events/' . $event_id)->toBeFile();
});


it('ignores file, when event is NOT in store', function () {
    $event_id = uniqid();

    $files = new Files(files_directory() . '/', fn() => true);
    $file = tempnam(sys_get_temp_dir(), 'file');
    file_put_contents($file, uniqid());
    $hash = hash_file('sha256', $file);

    $files($hash)($event_id, 'file://' . $file);
    expect(files_directory() . '/' . $hash)->not()->toBeFile();
    expect(files_directory() . '/' . $hash . '.events/' . $event_id)->not()->toBeFile();
});


it('removes files, when no events directory', function () {
    $hash = uniqid();

    expect(files_directory() . '/' . $hash . '.events')->not()->toBeDirectory();

    file_put_contents(files_directory() . '/' . $hash, uniqid());
    mkdir(files_directory() . '/' . $hash . '.events');
    expect(files_directory() . '/' . $hash)->toBeFile();
    expect(files_directory() . '/' . $hash . '.events')->toBeDirectory();

    $files = new Files(files_directory() . '/', fn() => true);

    expect(files_directory() . '/' . $hash)->not()->toBeFile();
    expect(files_directory() . '/' . $hash . '.events')->not()->toBeDirectory();
});

it('removes files, when no events in events directory exist', function () {
    $hash = uniqid();
    file_put_contents(files_directory() . '/' . $hash, uniqid());
    expect(files_directory() . '/' . $hash)->toBeFile();
    expect(files_directory() . '/' . $hash . '.events')->not()->toBeDirectory();

    $files = new Files(files_directory() . '/', fn() => true);

    expect(files_directory() . '/' . $hash)->not()->toBeFile();
});

it('removes files, when event is NOT in store', function () {
    $event_id = uniqid();

    $hash = uniqid();
    file_put_contents(files_directory() . '/' . $hash, uniqid());
    mkdir(files_directory() . '/' . $hash . '.events');
    touch(files_directory() . '/' . $hash . '.events/' . $event_id);
    expect(files_directory() . '/' . $hash)->toBeFile();
    expect(files_directory() . '/' . $hash . '.events')->toBeDirectory();
    expect(files_directory() . '/' . $hash . '.events/' . $event_id)->toBeFile();

    $files = new Files(files_directory() . '/', fn() => true);

    expect(files_directory() . '/' . $hash . '.events/' . $event_id)->not()->toBeFile();
    expect(files_directory() . '/' . $hash)->not()->toBeFile();
});
