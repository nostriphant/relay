<?php

namespace nostriphant\RelayTests;
use function \nostriphant\Relay\data_directory;

require_once dirname(__DIR__) . '/bootstrap.php';

define('files_directory()', data_directory() . '/files');

function files_directory() {
    return \nostriphant\Relay\data_directory() . '/files';
}
function make_files_directory() {
    return \nostriphant\Relay\make_data_directory() && (is_dir(files_directory()) || mkdir(files_directory()));
}
function destroy_files_directory() {
    return (is_dir(files_directory()) === false || rmdir(files_directory())) && \nostriphant\Relay\destroy_data_directory();
}