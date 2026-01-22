<?php

namespace nostriphant\Relay\Incoming\Req\Accepted;

use nostriphant\Relay\Subscriptions;

readonly class Limits {

    static function construct(
            \nostriphant\NIP01\Transmission $client,
            int $max_per_client = 10
    ): \nostriphant\Relay\Limits {
        $checks = [];

        if ($max_per_client > 0) {
            $checks['max number of subscriptions per client (' . $max_per_client . ') reached'] = fn(Subscriptions $subscriptions) => $subscriptions($client) >= $max_per_client;
        }

        return new \nostriphant\Relay\Limits($checks);
    }

    static function fromEnv(mixed ...$arguments): \nostriphant\Relay\Limits {
        return \nostriphant\Relay\Limits::fromEnv('REQ', __CLASS__, ...$arguments);
    }
}
