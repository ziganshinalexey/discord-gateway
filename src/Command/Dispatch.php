<?php

declare(strict_types = 1);

namespace Ziganshinalexey\DiscordGateway\Command;

use Ziganshinalexey\DiscordGateway\Command;
use Ziganshinalexey\DiscordGateway\State;

class Dispatch extends Command
{
    public function execute($json)
    {
        State::log('Execute: DISPATCH');

        $state = $this->getState();
        $type  = $json->t;

        State::log('Dispatch type: ' . $type);

        // Once we get our first dispatch, be sure we're sending a heartbeat


        $this->getState()->dispatch($type, $json);
    }
}