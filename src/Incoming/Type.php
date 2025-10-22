<?php

namespace nostriphant\Relay\Incoming;

interface Type {

    public function __invoke(array $payload): \Generator;
}
