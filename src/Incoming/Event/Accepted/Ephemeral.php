<?php

namespace nostriphant\Relay\Incoming\Event\Accepted;

use nostriphant\NIP01\Event;

class Ephemeral {

    public function __construct(
            private \nostriphant\Relay\Subscriptions $subscriptions
    ) {

    }

    public function __invoke(Event $event) {
        yield from ($this->subscriptions)($event);
    }
}
