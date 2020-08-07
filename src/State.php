<?php

declare(strict_types = 1);

namespace Ziganshinalexey\DiscordGateway;

use Ratchet\Client\WebSocket;
use React\EventLoop\LoopInterface;
use Ziganshinalexey\DiscordGateway\Command\Dispatch;
use Ziganshinalexey\DiscordGateway\Command\Heartbeat;
use Ziganshinalexey\DiscordGateway\Command\HeartbeatACK;
use Ziganshinalexey\DiscordGateway\Command\Hello;
use Ziganshinalexey\DiscordGateway\Command\Identify;

/**
 * Class State.
 */
class State
{
    /**
     * The current connection instance
     *
     * @var WebSocket
     */
    protected $connection;

    /**
     * Current bot token
     *
     * @var string
     */
    protected $token;

    /**
     * Loop instance
     *
     * @var LoopInterface
     */
    protected $loop;

    /**
     * Default heartbeat interval
     *
     * @var integer
     */
    protected $interval = 5;

    /**
     * Discord API operations to class relationships
     *
     * @var [type]
     */
    protected $ops = [
        0  => Dispatch::class,
        1  => Hello::class,
        2  => Identify::class,
        10 => Hello::class,
        11 => HeartbeatACK::class,
    ];

    /**
     * Current dispatch relationships
     *
     * @var array
     */
    protected $dispatch = [];

    /**
     * Current bot status (used in identify)
     *
     * @var string
     */
    protected $status = self::STATUS_DISCONNECTED;

    /**
     * Status constants
     *
     * @var string
     */
    const STATUS_DISCONNECTED = 'disconnected';
    const STATUS_CONNECTED    = 'connected';
    const STATUS_AUTHED       = 'authorized';

    /**
     * Init the State handler and set the connection, token and loop properties
     *
     * @param WebSocket     $connection Connection instance
     * @param string        $token      Bot token (from API)
     * @param LoopInterface $loop       Loop instance
     */
    public function __construct(WebSocket $connection, string $token, LoopInterface $loop)
    {
        $this->connection = $connection;
        $this->token      = $token;
        $this->loop       = $loop;
    }

    /**
     * Logging output method
     *
     * @param string $message Message to output
     */
    public static function log($message)
    {
        echo sprintf('[%s] %s%s', date('Y-m-d H:i:s'), $message, PHP_EOL);
    }

    /**
     * Get the current connection
     *
     * @return WebSocket instance
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Get the current token value
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Get the current event loop
     *
     * @return LoopInterface
     */
    public function getLoop()
    {
        return $this->loop;
    }

    /**
     * Get the current heartbeat interval
     *
     * @return int
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * Set the current heartbeat interval
     *
     * @param int $interval Interval in seconds
     */
    public function setInterval($interval)
    {
        $this->interval = $interval;
    }

    /**
     * Determine the action (command) to be taken based on the JSON input
     *
     * @param object $json JSON object, parsed from API response
     */
    public function action($json)
    {
        $op   = $json->op;
        $loop = $this->getLoop();

        // @todo: Пока переделать на мапу, потом на фабрику.
        $commandClass = $this->ops[$op];
        $command      = new $commandClass($this, $loop);
        $command->execute($json);
    }

    /**
     * Authorize the bot and update its state
     */
    public function authorize()
    {
        $loop = $this->getLoop();

        $command = new Identify($this, $loop);
        $command->execute(null);

        $this->getLoop()->addPeriodicTimer($this->getInterval(), [
            $this,
            'heartbeat',
        ]);

        $this->status = self::STATUS_AUTHED;
    }

    public function heartbeat()
    {
        $loop    = $this->getLoop();
        $command = new Heartbeat($this, $loop);
        $command->execute(null);
    }

    /**
     * Check the current state to see if the status is marked as authed (post-identify)
     *
     * @return boolean Authed/not authed status
     */
    public function isAuthed()
    {
        return ($this->status == self::STATUS_AUTHED);
    }

    /**
     * Set the dispatch array value
     *
     * @param array $dispatch Dispatch set
     */
    public function addDispatch(array $dispatch)
    {
        $this->dispatch = $dispatch;
    }

    /**
     * Dispatch the action (command) based on the type
     *
     * @param string $type Type of action
     * @param object $json JSON object
     *
     * @return mixed Result from call of dispatch handler
     */
    public function dispatch($type, $json)
    {
        $dispatch = $this->dispatch[$type] ?? null;
        if (null === $dispatch) {
            return null;
        }

        return $dispatch($json);
    }
}
