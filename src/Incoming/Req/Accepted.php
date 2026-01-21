<?php

namespace nostriphant\Relay\Incoming\Req;

use nostriphant\NIP01\Message;

class Accepted {

    public function __construct(
            private \nostriphant\Stores\Store $events,
            private \nostriphant\Relay\Subscriptions $subscriptions,
            private \nostriphant\Relay\Limits $limits,
    ) {

    }

    public function __invoke(string $subscription_id, array $filter_prototypes): mixed {
        yield from ($this->limits)($this->subscriptions)(
                        rejected: fn(string $reason) => yield Message::closed($subscription_id, $reason),
                        accepted: function () use ($subscription_id, $filter_prototypes) {
                            ($this->subscriptions)($subscription_id, $filter_prototypes);
                            yield from \nostriphant\Functional\Functions::iterator_map(($this->events)(...$filter_prototypes), fn(\nostriphant\NIP01\Event $event) => Message::event($subscription_id, $event));
                            yield Message::eose($subscription_id);
                        });
    }
}
