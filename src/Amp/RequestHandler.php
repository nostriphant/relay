<?php

namespace nostriphant\Relay\Amp;

use Amp\Http\HttpStatus;
use Amp\Websocket\Server\Websocket;
use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler as IRequestHandler;
use Amp\Http\Server\Response;

readonly class RequestHandler implements IRequestHandler {
    private \JsonSerializable $information_document;
    
    public function __construct(private Websocket $websocket, \JsonSerializable $information_document) {
        $this->information_document = $information_document;
    }
    public function __call(string $name, array $arguments): mixed {
        return $this->websocket->$name(...$arguments);
    }
    
    #[\Override]
    public function handleRequest(Request $request): Response {
        $response =  $this->websocket->handleRequest($request);
        if ($response->getStatus() === HttpStatus::UPGRADE_REQUIRED) {
            return new Response(
                headers: ['Content-Type' => 'application/json'],
                body: json_encode($this->information_document)
            );
        }
        
        return $response;
    }
}
