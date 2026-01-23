<?php

namespace nostriphant\Relay;

readonly class Relay {
    private InformationDocument $information_document;
    
    public function __construct(private Amp\WebsocketServer $server, string $relay_name, string $relay_description, string $relay_owner_npub, string $relay_contact) {
        $this->information_document = new \nostriphant\Relay\InformationDocument(
                name: $relay_name,
                description: $relay_description,
                pubkey: (new \nostriphant\NIP19\Bech32($relay_owner_npub))(),
                contact: $relay_contact,
                supported_nips: \nostriphant\Relay\Relay::enabled_nips(),
                software: \nostriphant\Relay\Relay::software(),
                version: \nostriphant\Relay\Relay::version()
        );
    }
    
    public function __invoke(\nostriphant\Stores\Store $store, \Closure $static_routes): callable {
        return ($this->server)(new \nostriphant\Relay\MessageHandlerFactory($store), $static_routes, $this->information_document);
    }
    
    public static function enabled_nips() : array {
        return [1, 2, 9, 11, 12, 13, 16, 20, 22, 33, 45];
    }
    
    public static function software() : string {
        return json_decode(file_get_contents(dirname(__DIR__) . '/composer.json'))->homepage;
    }
    
    public static function version() : string {
        return file_get_contents(dirname(__DIR__) . '/VERSION');
    }
}
