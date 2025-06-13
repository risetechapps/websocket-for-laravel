<?php

namespace RiseTechApps\WebSocket\Contracts;

use Ably\AblyRest;
use Pusher\Pusher;

abstract class WebSocketContract
{

    protected static $client = null;

    abstract public static function connect(array $config): static;

    abstract public function publish(string|array $channels, string $event, array $payload = [], string $socketId = null): mixed;

    abstract public function listChannels(): array;

    abstract public function getPresenceMembers($channelName): array;

    public function getClient(): Pusher|AblyRest|null
    {
        return static::$client;
    }

}
