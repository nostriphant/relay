<?php

namespace nostriphant\Relay\Amp;

use nostriphant\NIP01\Transmission;
use Amp\Websocket\WebsocketClient;
use Psr\Log\LoggerInterface;
use nostriphant\NIP01\Message;

class WebsocketTransmitter implements Transmission {
    public function __construct(private WebsocketClient $client, private LoggerInterface $log) {

    }
    #[\Override]
    public function __invoke(Message $message): bool {
        $this->log->debug('Sending ' . $message);
        $this->client->sendText($message);
        return true;
    }
}