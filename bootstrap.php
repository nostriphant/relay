<?php

namespace nostriphant\Relay;

require_once __DIR__ . '/vendor/autoload.php';

define('ROOT_DIR', __DIR__);
define('DATA_DIR', ROOT_DIR . '/data');
function make_data_directory() {
    return is_dir(DATA_DIR) || mkdir(DATA_DIR);
}
function destroy_data_directory() {
    return is_dir(DATA_DIR) === false || rmdir(DATA_DIR);
}