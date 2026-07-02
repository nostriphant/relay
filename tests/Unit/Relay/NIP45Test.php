<?php

use nostriphant\NIP01\Key;
use nostriphant\NIP01\Event\Unsigned;
use nostriphant\NIP01\Message;

it('accepts a simple COUNT message and returns the number of matching events', function () {
    $alice_key = self::key_sender();
    $bob_key = Key::generate();
    $store = \Pest\store([
        (new Unsigned(time(), 1, 'Hello world, from Alice!', []))($alice_key),
        (new Unsigned(time(), 1, 'Hello world, from Bob!', []))($bob_key)
    ]);

    $recipient = \Pest\handle(Message::count($id = uniqid(), [
                'authors' => [Key::derivePublicKey($alice_key)]
                    ], [
                'authors' => [Key::derivePublicKey($bob_key)]
            ]), \Pest\incoming($store));

    expect($recipient)->toHaveReceived(
        ['COUNT', $id, ['count' => 2]]
    );
});

it('refuses COUNT message without filters', function () {
    $recipient = \Pest\handle(Message::count($id = uniqid(), []));

    expect($recipient)->toHaveReceived(
            ['CLOSED', $id, 'count filters are empty']
    );
});

it('refuses COUNT message with more than max filters (default 10)', function () {
    $recipient = \Pest\handle(Message::count($id = uniqid(),
                    ['ids' => ['a']],
                ['ids' => ['a']],
                ['ids' => ['a']],
                ['ids' => ['a']],
                ['ids' => ['a']],
                ['ids' => ['a']],
                ['ids' => ['a']],
                ['ids' => ['a']],
                ['ids' => ['a']],
                ['ids' => ['a']],
                ['ids' => ['a']]
    ));

    expect($recipient)->toHaveReceived(
            ['CLOSED', $id, 'max number of filters per count (10) reached']
    );
});
