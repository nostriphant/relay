<?php

namespace nostriphant\Relay\Amp;

use \Psr\Log\LoggerInterface;
use nostriphant\Relay\Amp\WebsocketClientHandler;
use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Http\Server\DefaultErrorHandler;
use Amp\Http\Server\Router;
use Amp\Http\Server\SocketHttpServer;
use Amp\Websocket\Server\Websocket;
use Amp\Websocket\Server\Rfc6455Acceptor;
use Amp\Http\Server\RequestHandler\ClosureRequestHandler;

readonly class WebsocketServer {
    
    private \Amp\Http\Server\HttpServer $server;
    
    public function __construct(string $socket, int $max_connections_per_ip, private LoggerInterface $log) {
        $this->server = SocketHttpServer::createForDirectAccess($this->log, connectionLimitPerIp: $max_connections_per_ip);
        $this->server->expose($socket);
    }

    public function __invoke(MessageHandlerFactory $messageHandlerFactory, \Closure $static_routes, \nostriphant\Relay\InformationDocument $information_document): callable {
        $errorHandler = new DefaultErrorHandler();
        $clientHandler = new WebsocketClientHandler($messageHandlerFactory, $this->log);   

        $router = new Router($this->server, $this->log, $errorHandler);
        $acceptor = new Rfc6455Acceptor();
        //$acceptor = new AllowOriginAcceptor(
        //    ['http://localhost:' . $port, 'http://127.0.0.1:' . $port, 'http://[::1]:' . $port],
        //);
        
        $websocket = new Websocket($this->server, $this->log, $acceptor, $clientHandler);
        
        $router->addRoute('GET', '/', new ClosureRequestHandler(function(Request $request) use ($information_document, $websocket): Response {
            if ($request->getHeader('Accept') === 'application/nostr+json') {
                return new Response(
                    headers: ['Content-Type' => 'application/nostr+json'],
                    body: json_encode($information_document)
                );
            }
            return $websocket->handleRequest($request);
        }));

        $static_routes(fn(string $method, string $route, callable $endpoint) => $router->addRoute($method, $route, new ClosureRequestHandler(fn(Request $request) => new Response(...$endpoint(...$request->getAttribute(Router::class))))));
        
        $this->server->start($router, $errorHandler);

        return fn() => $this->server->stop();
    }

}
