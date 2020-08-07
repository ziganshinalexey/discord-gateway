<?php

declare(strict_types = 1);

namespace Ziganshinalexey\DiscordGateway\Command;

use Ziganshinalexey\DiscordGateway\Command;
use Ziganshinalexey\DiscordGateway\State;

class Hello extends Command
{
    public function execute($json)
    {
        State::log('Execute: HELLO');

        $interval = ((int)$json->d->heartbeat_interval / 1000) - 2;
        $state    = $this->getState();
        $state->setInterval($interval);
        $state->authorize();
    }
}