<?php

namespace nostriphant\Relay;

readonly class InformationDocument implements \JsonSerializable {
    
    public function __construct(
            private string $name, 
            private string $description, 
            private string $pubkey, 
            private string $contact,
            private array $supported_nips,
            private string $software,
            private string $version
    ) {
        
    }
    
    public function __invoke(): array {
        return [
            "name" => $this->name,
            "description" => $this->description,
            "pubkey" => $this->pubkey,
            "contact" => $this->contact,
            "supported_nips" => $this->supported_nips,
            "software" => $this->software,
            "version" => $this->version
        ];
    }
    
    #[\Override]
    public function jsonSerialize(): array {
        return $this();
    }

    static function generate(string $name, string $description, string $pubkey, string $contact) {
        return (new self(
            name: $name,
            description: $description,
            pubkey: $pubkey,
            contact: $contact,
            supported_nips: [1, 2, 9, 11, 12, 13, 16, 20, 22, 33, 45, 92, 94],
            software: 'Transpher',
            version: TRANSPHER_VERSION
        ))();
    }
}
