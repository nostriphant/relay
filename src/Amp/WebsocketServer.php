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
use \nostriphant\Functional\FunctionList;

readonly class WebsocketServer {
    
    private \Amp\Http\Server\HttpServer $server;
    
    public function __construct(string $socket, int $max_connections_per_ip, private LoggerInterface $log, private FunctionList $static_routes) {
        
        $this->server = SocketHttpServer::createForDirectAccess($this->log, connectionLimitPerIp: $max_connections_per_ip);
        $this->server->expose($socket);
    }

    public function __invoke(\nostriphant\Stores\Store $store): callable {
        $errorHandler = new DefaultErrorHandler();
        $clientHandler = new WebsocketClientHandler(new \nostriphant\Relay\MessageHandlerFactory($store), $this->log);   

        $router = new Router($this->server, $this->log, $errorHandler);
        $acceptor = new Rfc6455Acceptor();
        //$acceptor = new AllowOriginAcceptor(
        //    ['http://localhost:' . $port, 'http://127.0.0.1:' . $port, 'http://[::1]:' . $port],
        //);
        
        $websocket = new Websocket($this->server, $this->log, $acceptor, $clientHandler);
        
        ($this->static_routes)(function(string|array $method, string $route, callable $endpoint) use ($router, $websocket) : void {
            $handler = new ClosureRequestHandler(function(Request $request) use ($route, $endpoint, $websocket) { 
                $response = $endpoint($route === '/' ? fn() => $websocket->handleRequest($request) : $request->getAttribute(Router::class), $request->getHeaders());
                if (is_array($response)) {
                    return new Response(...$response);
                }
                return $response;
            });
            
            match (is_string($method)) {
                true => $router->addRoute($method, $route, $handler),
                false => array_map(fn(string $method) => $router->addRoute($method, $route, $handler), $method)
            };
        });
        
        $this->server->start($router, $errorHandler);

        return fn() => $this->server->stop();
    }

}
