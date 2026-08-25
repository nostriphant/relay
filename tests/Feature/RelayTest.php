<?php

namespace nostriphant\RelayTests\Feature;

it('boots a relay instance, which responds with an NIP-11 information document on a "GET /" request', function() {
    $body = $this->expectRelayResponse('/', 200, 'application/nostr+json', headers:['Accept: application/nostr+json']);
    expect($body)->tobe(json_encode([
            'name' => 'Transpher Relay',
            'description' => 'Some interesting description goes here',
            'pubkey' => 'c0bb181bc39c4e59768805bbc5bdd34c508f14b01a298d63be4510d97417ce01',
            'contact' =>'nostriphant@rikmeijer.nl',
            'supported_nips' => [1, 2, 9, 11, 12, 13, 16, 20, 22, 33, 45],
            'software' => "https://github.com/nostriphant/relay",
            'version' => "2.2.0"
    ]));
});


it('boots a relay instance, which responds with an NIP-11 information document on a "GET " request', function() {
    $body = $this->expectRelayResponse('', 200, 'application/nostr+json', headers:['Accept: application/nostr+json']);
    expect($body)->tobe(json_encode([
            'name' => 'Transpher Relay',
            'description' => 'Some interesting description goes here',
            'pubkey' => 'c0bb181bc39c4e59768805bbc5bdd34c508f14b01a298d63be4510d97417ce01',
            'contact' =>'nostriphant@rikmeijer.nl',
            'supported_nips' => [1, 2, 9, 11, 12, 13, 16, 20, 22, 33, 45],
            'software' => "https://github.com/nostriphant/relay",
            'version' => "2.2.0"
    ]));
});
