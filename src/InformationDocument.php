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
}
