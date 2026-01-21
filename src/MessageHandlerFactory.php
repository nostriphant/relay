<?php

namespace nostriphant\Relay;

use \Psr\Log\LoggerInterface;

readonly class MessageHandlerFactory implements \nostriphant\Relay\Amp\MessageHandlerFactory {
    
    private Incoming $incoming;
    
    public function __construct(\nostriphant\Stores\Store $store, Files $files, private LoggerInterface $log) {
        $this->incoming = new Incoming($store, $files);
    }
    
    #[\Override]
    public function __invoke(\nostriphant\NIP01\Transmission $transmission) : \nostriphant\Relay\Amp\MessageHandler {
        return new MessageHandler($this->incoming, $transmission, $this->log);
    }
}
