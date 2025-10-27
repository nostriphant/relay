<?php

namespace nostriphant\Relay;

readonly class InformationDocument implements \JsonSerializable {
    
    public function __construct(
            private string $name, 
            private string $description, 
            private string $pubkey, 
            private string $contact
    ) {
        
    }
    
    public function __invoke(): array {
        return [
            "name" => $this->name,
            "description" => $this->description,
            "pubkey" => $this->pubkey,
            "contact" => $this->contact,
            "supported_nips" => Relay::enabled_nips(),
            "software" => Relay::software(),
            "version" => Relay::version()
        ];
    }
    
    #[\Override]
    public function jsonSerialize(): array {
        return $this();
    }
}
