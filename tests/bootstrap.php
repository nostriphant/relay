<?php

namespace nostriphant\RelayTests;

require_once dirname(__DIR__) . '/bootstrap.php';

define('FILES_DIR', DATA_DIR . '/files');

function make_files_directory() {
    return \nostriphant\Relay\make_data_directory() && (is_dir(FILES_DIR) || mkdir(FILES_DIR));
}
function destroy_files_directory() {
    return (is_dir(FILES_DIR) === false || rmdir(FILES_DIR)) && \nostriphant\Relay\destroy_data_directory();
}