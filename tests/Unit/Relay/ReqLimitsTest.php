<?php

it('has a maximum number of subscriptions per connected client.', function () {
    $subscriptions = \Pest\subscriptions($relay = \Pest\relay());

    $limits = \nostriphant\Relay\Incoming\Req\Accepted\Limits::construct(max_per_client: 1, client:$relay);

    expect($limits($subscriptions, ['ids' => ['a']]))->toHaveState(accepted: '*');

    $subscriptions('sub-id', [['ids' => ['a']]]);

    expect($limits($subscriptions, ['ids' => ['a']]))->toHaveState(rejected: ['max number of subscriptions per client (1) reached']);
});

it('has a maximum number of subscriptions per connected client. Defaults to 10.', function () {
    $subscriptions = \Pest\subscriptions($relay = \Pest\relay());

    $limits = \nostriphant\Relay\Incoming\Req\Accepted\Limits::construct(client:$relay);


    expect($limits($subscriptions, ['ids' => ['a']]))->toHaveState(accepted: '*');

    for ($i = 0; $i < 10; $i++) {
        $subscriptions('sub-id' . $i, [['ids' => ['a']]]);
    }

    expect($limits($subscriptions, ['ids' => ['a']]))->toHaveState(rejected: ['max number of subscriptions per client (10) reached']);
});


it('has a maximum number of subscriptions per connected client. Disabled when set to zero.', function () {
    $subscriptions = \Pest\subscriptions($relay = \Pest\relay());

    $limits = \nostriphant\Relay\Incoming\Req\Accepted\Limits::construct(max_per_client: 0, client:$relay);

    expect($limits($subscriptions, ['ids' => ['a']]))->toHaveState(accepted: '*');

    for ($i = 0; $i < 100; $i++) {
        $subscriptions('sub-id' . $i, [['ids' => ['a']]]);
    }

    expect($limits($subscriptions, ['ids' => ['a']]))->toHaveState(accepted: '*');
});

it('has a maximum number of subscriptions per connected client, configurable through env-vars. Defaults to 10. Disabled when set to zero.', function () {
    $subscriptions = \Pest\subscriptions($relay = \Pest\relay());

    putenv('LIMIT_REQ_MAX_PER_CLIENT=1');
    $limits = \nostriphant\Relay\Incoming\Req\Accepted\Limits::fromEnv($relay);

    expect($limits($subscriptions, ['ids' => ['a']]))->toHaveState(accepted: '*');

    $subscriptions('sub-id', [['ids' => ['a']]]);

    expect($limits($subscriptions, ['ids' => ['a']]))->toHaveState(rejected: ['max number of subscriptions per client (1) reached']);
    putenv('LIMIT_REQ_MAX_PER_CLIENT');
});
