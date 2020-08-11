<?php

declare(strict_types = 1);

namespace Ziganshinalexey\DiscordGateway;

use Ratchet\Client\Connector as RatchetConnector;
use Ratchet\Client\WebSocket;
use Ratchet\RFC6455\Messaging\MessageInterface;
use React\EventLoop\Factory;
use React\Socket\Connector;

/**
 * Class Bot.
 */
class Bot
{
    /**
     * Default WSS URL (from the Discord API docs)
     *
     * @var string
     */
    protected $wssUrl = 'wss://gateway.discord.gg/?v=6&encoding=json';

    /**
     * Current bot token
     *
     * @var string
     */
    protected $token;

    /**
     * Current set of dispatch handlers
     *
     * @var [type]
     */
    protected $dispatch = [];

    /**
     * Init the bot and set the token and, optionally, the WSS URL
     *
     * @param string $botToken Current bot token
     */
    public function __construct($botToken)
    {
        $this->token = $botToken;
    }

    /**
     * Add a new dispatch handler
     *
     * @param string          $type     Dispatch type
     * @param string|Callable $callback Callback to execute when dispatching action
     */
    public function addDispatch($type, $callback)
    {
        $this->dispatch[$type] = $callback;
    }

    /**
     * Init the bot and set up the loop/actions for the WebSocket
     */
    public function init()
    {
        $loop           = Factory::create();
        $reactConnector = new Connector($loop);
        $connector      = new RatchetConnector($loop, $reactConnector);
        $token          = $this->token;
        $dispatch       = $this->dispatch;

        $connector($this->wssUrl)->then(function(WebSocket $connection) use ($token, $loop, $dispatch) {
            $state = new State($connection, $token, $loop);
            $state->addDispatch($dispatch);

            $connection->on('message', function(MessageInterface $msg) use ($connection, $state, $loop) {
                $payload = $msg->getPayload();
                echo $payload . PHP_EOL;
                $json = json_decode($payload);
                $state->action($json);
            });

            $connection->on('close', function() use ($connection) {
                $connection->close();
                echo 'Connection closed' . PHP_EOL;
            });
        }, function(\Exception $e) use ($loop) {
            echo "Could not connect: {$e->getMessage()}\n";
            $loop->stop();
        });

        $loop->run();
    }
}
