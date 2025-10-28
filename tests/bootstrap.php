<?php

namespace nostriphant\RelayTests;
use function \nostriphant\Relay\data_directory;

require_once dirname(__DIR__) . '/bootstrap.php';

function files_directory() {
    return data_directory() . '/files';
}
function make_files_directory() {
    return \nostriphant\Relay\make_data_directory() && (is_dir(files_directory()) || mkdir(files_directory()));
}

function destroy_directories($path) {
    foreach (glob($path . '/*') as $node) {
        if (in_array(basename($node), ['.', '..'])) {
            continue;
        } elseif (is_file($node)) {
            unlink($node);
        } elseif (is_dir($node)) {
            destroy_directories($node);
            rmdir($node);
        }
    }
}

function destroy_files_directory() {
    destroy_directories(files_directory());
    return (is_dir(files_directory()) === false || rmdir(files_directory())) && \nostriphant\Relay\destroy_data_directory();
}