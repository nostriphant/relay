<?php


namespace nostriphant\Relay;


class Routes {
    
    private array $routes;
    
    public function __construct(callable ...$routes) {
        $this->routes = $routes;
    }
    
    public function bind(callable $route) : self {
        return new self($route, ...$this->routes);
    }
    
    public function __invoke(callable $define): mixed {
        return array_map(fn(callable $route) => $route($define), $this->routes);
    }
    
}
