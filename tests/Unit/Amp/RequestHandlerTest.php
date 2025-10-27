<?php

it('can send a Response containing an NIP-11 compliant information document', function () {

    
    $_SERVER['RELAY_NAME'] = 'Transpher Relay';
    $_SERVER['RELAY_DESCRIPTION'] = 'Some interesting description goes here';
    $_SERVER['RELAY_OWNER_NPUB'] = (string) nostriphant\NIP19\Bech32::npub('c0bb181bc39c4e59768805bbc5bdd34c508f14b01a298d63be4510d97417ce01');
    $_SERVER['RELAY_CONTACT'] = 'transpher@nostriphant.dev';
    
    $request = new Amp\Http\Server\Request(
            Mockery::mock(Amp\Http\Server\Driver\Client::class),
            'GET',
            Mockery::mock(Psr\Http\Message\UriInterface::class)
    );
    
    $httpserver = Mockery::mock(\Amp\Http\Server\HttpServer::class);
    $httpserver->expects('onStop');
    
    $acceptor = Mockery::mock(\Amp\Websocket\Server\WebsocketAcceptor::class);
    $acceptor->expects('handleHandshake')->with($request)->andReturn(new Amp\Http\Server\Response(
            status: Amp\Http\HttpStatus::UPGRADE_REQUIRED
    ));
    $websocket = new Amp\Websocket\Server\Websocket(
           $httpserver,
            Mockery::mock(\Psr\Log\LoggerInterface::class),
            $acceptor,
            Mockery::mock(\Amp\Websocket\Server\WebsocketClientHandler::class)
    );
    
    $handler = new \nostriphant\Relay\Amp\RequestHandler($websocket, $document = new nostriphant\Relay\InformationDocument(
            name: 'Transpher Relay',
            description: 'Some interesting description goes here',
            pubkey: 'c0bb181bc39c4e59768805bbc5bdd34c508f14b01a298d63be4510d97417ce01',
            contact: 'transpher@nostriphant.dev',
            supported_nips: \nostriphant\Relay\Relay::enabled_nips(),
            software: \nostriphant\Relay\Relay::software(),
            version: \nostriphant\Relay\Relay::version()
    ));
    
    $response = $handler->handleRequest($request);
    
    expect($response->getHeader('Content-Type'))->toBe('application/json');
    
    $body = $response->getBody()->read();
    expect($body)->toBeJson();
    expect($body)->tobe(json_encode($document));
});