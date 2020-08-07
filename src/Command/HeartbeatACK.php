<?php

declare(strict_types = 1);

namespace Ziganshinalexey\DiscordGateway\Command;

use Ziganshinalexey\DiscordGateway\Command;
use Ziganshinalexey\DiscordGateway\State;

class HeartbeatACK extends Command
{
    public function execute($json)
    {
        State::log('Execute: HEARTBEAT-ACK');
    }
}