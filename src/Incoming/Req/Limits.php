<?php

namespace nostriphant\Relay\Incoming\Req;

use nostriphant\Relay\Subscriptions;

readonly class Limits {

    static function construct(
            int $max_filters_per_subscription = 10
    ): \nostriphant\Relay\Limits {
        $checks = [];

        if ($max_filters_per_subscription > 0) {
            $checks['subscription filters are empty'] = fn(array $filter_prototypes) => count($filter_prototypes) === 0;
        }
        if ($max_filters_per_subscription > 0) {
            $checks['max number of filters per subscription (' . $max_filters_per_subscription . ') reached'] = fn(array $filter_prototypes) => count($filter_prototypes) > $max_filters_per_subscription;
        }

        return new \nostriphant\Relay\Limits($checks);
    }

    static function fromEnv(): \nostriphant\Relay\Limits {
        return \nostriphant\Relay\Limits::fromEnv('REQ', __CLASS__);
    }
}
