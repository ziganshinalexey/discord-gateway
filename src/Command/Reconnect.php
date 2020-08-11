<?php

declare(strict_types = 1);

namespace Ziganshinalexey\DiscordGateway\Command;

use Ziganshinalexey\DiscordGateway\Command;
use Ziganshinalexey\DiscordGateway\State;

class Reconnect extends Command
{
    public function execute($json)
    {
        State::log('Execute: RECONNECT');

        $json = json_encode([
            'op' => 6,
            'd'  => [
                'session_id' => $this->getState()->getSessionId(),
                'seq'        => $this->getState()->getSequence(),
                'token'      => $this->getState()->getToken(),
            ],
        ]);

        return $this->getConnection()->send($json);
    }
}