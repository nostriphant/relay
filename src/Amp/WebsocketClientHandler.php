<?php

namespace nostriphant\Relay\Amp;

use Amp\Websocket\Server\WebsocketGateway;
use Amp\Websocket\Server\WebsocketClientGateway;
use Amp\Websocket\WebsocketClient;
use nostriphant\NIP01\Message;
use nostriphant\NIP01\Transmission;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;

readonly class WebsocketClientHandler implements \Amp\Websocket\Server\WebsocketClientHandler {
    
    private WebsocketGateway $gateway;
    
    public function __construct(private MessageHandlerFactory $message_handler_factory, private \Psr\Log\LoggerInterface $log) {
        $this->gateway = new WebsocketClientGateway();
    }

    #[\Override]
    public function handleClient(WebsocketClient $client, Request $request, Response $response): void {
        $this->gateway->addClient($client);
        
        $message_handler = ($this->message_handler_factory)(new class($client, $this->log) implements Transmission {
            public function __construct(private WebsocketClient $client, private \Psr\Log\LoggerInterface $log) {

            }
            #[\Override]
            public function __invoke(Message $message): bool {
                $this->log->debug('Sending ' . $message);
                $this->client->sendText($message);
                return true;
            }

        });
        
        foreach ($client as $message) {
            $json = (string)$message;
            $this->log->debug('Received json ' . $json);
            $message_handler($json);
        }
    }
}
