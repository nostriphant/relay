<?php

namespace nostriphant\Relay\Amp;

use \Psr\Log\LoggerInterface;
use nostriphant\Relay\Amp\WebsocketClientHandler;
use Amp\Websocket\Server\WebsocketClientGateway;
use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Http\Server\DefaultErrorHandler;
use Amp\Http\Server\Router;
use Amp\Http\Server\SocketHttpServer;
use Amp\Websocket\Server\Websocket;
use Amp\Websocket\Server\Rfc6455Acceptor;
use Amp\Http\Server\RequestHandler\ClosureRequestHandler;

readonly class WebsocketServer {
    
    private \Amp\Websocket\Server\WebsocketClientHandler $clientHandler;
    
    public function __construct(MessageHandlerFactory $messageHandlerFactory, private LoggerInterface $log, private \Closure $static_routes) {
        $this->clientHandler = new WebsocketClientHandler($messageHandlerFactory, new WebsocketClientGateway());   
    }

    public function __invoke(string $socket, int $max_connections_per_ip, \nostriphant\Relay\InformationDocument $information_document): callable {
        $errorHandler = new DefaultErrorHandler();
        
        $server = SocketHttpServer::createForDirectAccess($this->log, connectionLimitPerIp: $max_connections_per_ip);
        $server->expose($socket);

        $router = new Router($server, $this->log, $errorHandler);
        $acceptor = new Rfc6455Acceptor();
        //$acceptor = new AllowOriginAcceptor(
        //    ['http://localhost:' . $port, 'http://127.0.0.1:' . $port, 'http://[::1]:' . $port],
        //);
        
        $websocket = new Websocket($server, $this->log, $acceptor, $this->clientHandler);
        
        $router->addRoute('GET', '/', new ClosureRequestHandler(function(Request $request) use ($information_document, $websocket): Response {
            $response =  $websocket->handleRequest($request);
            if ($response->getStatus() === \Amp\Http\HttpStatus::UPGRADE_REQUIRED) {
                return new Response(
                    headers: ['Content-Type' => 'application/json'],
                    body: json_encode($information_document)
                );
            }

            return $response;
        }));

        ($this->static_routes)(fn(string $method, string $route, callable $endpoint) => $router->addRoute($method, $route, new ClosureRequestHandler(fn(Request $request) => new Response(...$endpoint(...$request->getAttribute(Router::class))))));
        
        $server->start($router, $errorHandler);

        return fn() => $server->stop();
    }

}
