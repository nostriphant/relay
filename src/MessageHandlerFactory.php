<?php

namespace nostriphant\Relay;

readonly class MessageHandlerFactory implements \nostriphant\Relay\Amp\MessageHandlerFactory {
    
    private Incoming $incoming;
    
    public function __construct(\nostriphant\Stores\Store $store) {
        $this->incoming = new Incoming($store);
    }
    
    #[\Override]
    public function __invoke(\nostriphant\NIP01\Transmission $transmission) : \nostriphant\Relay\Amp\MessageHandler {
        return new MessageHandler($this->incoming, $transmission);
    }
}
