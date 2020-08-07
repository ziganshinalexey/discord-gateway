<?php

declare(strict_types = 1);

namespace Ziganshinalexey\DiscordGateway\Command;

use Ziganshinalexey\DiscordGateway\Command;
use Ziganshinalexey\DiscordGateway\State;

class Identify extends Command
{
    public function execute($json)
    {
        State::log('Execute: IDENTIFY');

        $json = json_encode([
            'op' => 2,
            'd'  => [
                'token'      => $this->getState()->getToken(),
                'properties' => [
                    '$os'               => PHP_OS,
                    '$browser'          => 'Userstory',
                    '$device'           => 'Userstory',
                    '$referrer'         => 'https://github.com/ziganshinalexey/discord-gateway',
                    '$referring_domain' => 'https://github.com/ziganshinalexey/discord-gateway',
                ],
                'compress'   => false,
                'shard'      => [
                    0,
                    1,
                ],
            ],
        ]);

        return $this->getConnection()->send($json);
    }
}