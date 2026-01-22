<?php

namespace nostriphant\Relay\Incoming\Event\Accepted\Regular;

use nostriphant\NIP01\Event;
use nostriphant\Functional\Alternate;
interface Kind {

    public function __construct(\nostriphant\Stores\Store $store);

    static function validate(Event $event): Alternate;

    public function __invoke(Event $event): void;
}
