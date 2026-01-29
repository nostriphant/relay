<?php

namespace nostriphant\Relay;

readonly class Relay {
    private InformationDocument $information_document;
    private MessageHandlerFactory $message_handler_factory;
    
    public function __construct(\nostriphant\Stores\Store $store, string $relay_name, string $relay_description, string $relay_owner_npub, string $relay_contact) {
        $this->message_handler_factory = new \nostriphant\Relay\MessageHandlerFactory($store);
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
    
    public function __invoke(Amp\WebsocketServer $server) : callable {
        return $server($this->message_handler_factory , $this->information_document);
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
