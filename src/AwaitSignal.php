<?php

namespace nostriphant\Relay;

use \nostriphant\Functional\Await;

readonly class AwaitSignal {
    public function __construct(callable $callback) {
       (new Await(fn() => \Amp\trapSignal([SIGINT, SIGTERM])))($callback);
    }
}
