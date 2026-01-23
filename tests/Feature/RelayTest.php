<?php

namespace nostriphant\RelayTests\Feature;

it('boots a relay instance, which responds with an NIP-11 information document on a "GET /" request', function() {
    $body = $this->expectRelayResponse('/', 200, 'application/nostr+json', headers:['Accept: application/nostr+json']);
    expect($body)->tobe(json_encode([
            'name' => 'Transpher Relay',
            'description' => 'Some interesting description goes here',
            'pubkey' => 'c0bb181bc39c4e59768805bbc5bdd34c508f14b01a298d63be4510d97417ce01',
            'contact' =>'transpher@nostriphant.dev',
            'supported_nips' => \nostriphant\Relay\Relay::enabled_nips(),
            'software' => \nostriphant\Relay\Relay::software(),
            'version' => \nostriphant\Relay\Relay::version()
    ]));
});


it('boots a relay instance, which responds with an NIP-11 information document on a "GET " request', function() {
    $body = $this->expectRelayResponse('', 200, 'application/nostr+json', headers:['Accept: application/nostr+json']);
    expect($body)->tobe(json_encode([
            'name' => 'Transpher Relay',
            'description' => 'Some interesting description goes here',
            'pubkey' => 'c0bb181bc39c4e59768805bbc5bdd34c508f14b01a298d63be4510d97417ce01',
            'contact' =>'transpher@nostriphant.dev',
            'supported_nips' => \nostriphant\Relay\Relay::enabled_nips(),
            'software' => \nostriphant\Relay\Relay::software(),
            'version' => \nostriphant\Relay\Relay::version()
    ]));
});