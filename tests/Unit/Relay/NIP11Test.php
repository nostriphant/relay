<?php

use nostriphant\Relay\InformationDocument;

it('generates Relay Information Document', function () {

    $name = 'Transpher Relay';
    $description = 'Some interesting description goes here';
    $owner_pubkey = 'c0bb181bc39c4e59768805bbc5bdd34c508f14b01a298d63be4510d97417ce01';
    $contact = 'transpher@nostriphant.dev';
    $nips = [0,1,2];
    $software = 'MyRelaySoftware';
    $version = '1.0.1';

    expect(call_user_func(new InformationDocument($name, $description, $owner_pubkey, $contact, $nips, $software, $version)))->toBe([
        "name" => 'Transpher Relay',
        "description" => 'Some interesting description goes here',
        "pubkey" => 'c0bb181bc39c4e59768805bbc5bdd34c508f14b01a298d63be4510d97417ce01',
        "contact" => 'transpher@nostriphant.dev',
        "supported_nips" => [0,1,2],
        "software" => 'MyRelaySoftware',
        "version" => '1.0.1'
    ]);
});