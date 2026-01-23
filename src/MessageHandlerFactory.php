<?php

namespace nostriphant\Relay;

use \Psr\Log\LoggerInterface;

readonly class MessageHandlerFactory implements \nostriphant\Relay\Amp\MessageHandlerFactory {
    
    private Incoming $incoming;
    
    public function __construct(\nostriphant\Stores\Store $store) {
        $this->incoming = new Incoming($store);
    }
    
    #[\Override]
    public function __invoke(\nostriphant\NIP01\Transmission $transmission, LoggerInterface $log) : \nostriphant\Relay\Amp\MessageHandler {
        return new MessageHandler($this->incoming, $transmission, $log);
    }
}
