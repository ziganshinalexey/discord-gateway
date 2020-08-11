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

        $type = $json->t;
        State::log('Dispatch type: ' . $type);

        $this->getState()->dispatch($type, $json);
    }
}