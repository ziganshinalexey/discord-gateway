<?php

declare(strict_types = 1);

namespace Ziganshinalexey\DiscordGateway\Command;

use Ziganshinalexey\DiscordGateway\Command;
use Ziganshinalexey\DiscordGateway\State;

class Heartbeat extends Command
{
    public function execute($json)
    {
        State::log('Execute: HEARTBEAT');

        $json = json_encode([
            'op' => 1,
            'd'  => 1,
        ]);

        $this->getConnection()->send($json);
    }
}