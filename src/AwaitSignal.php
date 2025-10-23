<?php

namespace nostriphant\Relay;

readonly class AwaitSignal {
    private \nostriphant\Functional\Await $await;
    
    public function __construct(callable $callback) {
       (new Await(fn() => \Amp\trapSignal([SIGINT, SIGTERM])))($callback);
    }
}
