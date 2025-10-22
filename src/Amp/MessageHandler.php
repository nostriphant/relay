<?php

namespace nostriphant\Relay\Amp;

interface MessageHandler {
    function __invoke(string $json) : void;
}
