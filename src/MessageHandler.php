<?php

namespace nostriphant\Relay;

use \Psr\Log\LoggerInterface;
use nostriphant\NIP01\Message;
use nostriphant\NIP01\Transmission;

readonly class MessageHandler implements \nostriphant\Relay\Amp\MessageHandler {
    
    private Subscriptions $subscriptions;
    
    public function __construct(private Incoming $incoming, private Transmission $transmission, private LoggerInterface $log) {
        $this->subscriptions = new Subscriptions($transmission);
    }
    
    #[\Override]
    public function __invoke(string $json) : void {
        $this->log->debug('Received json ' . $json);
        try {
            foreach (($this->incoming)($this->subscriptions, Message::decode($json)) as $reply) {
                ($this->transmission)($reply);
            }
        } catch (\InvalidArgumentException $ex) {
            ($this->transmission)(Message::notice($ex->getMessage()));
        }
    }       
}
