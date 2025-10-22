<?php

namespace nostriphant\Relay\Incoming\Count;

readonly class Limits {

    static function construct(
            int $max_filters = 10
    ): \nostriphant\Relay\Limits {
        $checks = [];

        $checks['count filters are empty'] = fn(array $filter_prototypes) => count($filter_prototypes) === 0;

        if ($max_filters > 0) {
            $checks['max number of filters per count (' . $max_filters . ') reached'] = fn(array $filter_prototypes) => count($filter_prototypes) > $max_filters;
        }

        return new \nostriphant\Relay\Limits($checks);
    }

    static function fromEnv(): \nostriphant\Relay\Limits {
        return \nostriphant\Relay\Limits::fromEnv('COUNT', __CLASS__);
    }
}
