<?php

namespace nostriphant\Relay\Amp;


interface MessageHandlerFactory {
    function __invoke(\nostriphant\NIP01\Transmission $transmission) : MessageHandler;
    
}
