<?php

declare(strict_types = 1);

namespace Ziganshinalexey\DiscordGateway;

use Ratchet\Client\WebSocket;
use React\EventLoop\LoopInterface;

/**
 * Class Command.
 */
abstract class Command
{
    /**
     * Current bot instance
     *
     * @var State
     */
    protected $state;

    /**
     * Curernt loop instance
     *
     * @var LoopInterface
     */
    protected $loop;

    /**
     * Init the instance and set the bot and loop instance
     *
     * @param State         $state State instance
     * @param LoopInterface $loop  EventLoop instance
     */
    public function __construct(&$state, &$loop)
    {
        $this->state = $state;
        $this->loop  = $loop;
    }

    /**
     * Get the current bot instance
     *
     * @return State
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Get the current event loop instance
     *
     * @return LoopInterface
     */
    public function getLoop()
    {
        return $this->loop;
    }

    /**
     * Get the current connection from the Bot
     *
     * @return WebSocket
     */
    public function getConnection()
    {
        return $this->getState()->getConnection();
    }

    /**
     * Abstract method definition for child class actions
     *
     * @var object $json JSON object
     */
    public abstract function execute($json);
}