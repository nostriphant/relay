<?php



namespace nostriphant\Relay\Blossom;

interface Endpoint {
    public function __invoke(callable $define) : array;
}
