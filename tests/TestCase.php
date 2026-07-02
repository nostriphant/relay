<?php

namespace nostriphant\RelayTests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use nostriphant\NIP01\Key;

abstract class TestCase extends BaseTestCase
{
    private static function key(string $hex): Key {
        return Key::fromHex($hex);
    }

    static function key_sender(): Key {
        return self::key('a71a415936f2dd70b777e5204c57e0df9a6dffef91b3c78c1aa24e54772e33c3');
    }

    static function pubkey_sender(): string {
        return Key::derivePublicKey(self::key_sender());
    }

    static function key_recipient(): Key {
        return self::key('6eeb5ad99e47115467d096e07c1c9b8b41768ab53465703f78017204adc5b0cc');
    }

    static function pubkey_recipient(): string {
        return Key::derivePublicKey(self::key_recipient());
    }
}
