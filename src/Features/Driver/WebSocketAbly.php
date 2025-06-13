<?php

namespace RiseTechApps\WebSocket\Features\Driver;

use Ably\AblyRest;
use Exception;
use RiseTechApps\WebSocket\Contracts\WebSocketContract;


class WebSocketAbly extends WebSocketContract
{
    /**
     * @throws Exception
     */
    public static function connect(array $config): static
    {
        try {
            static::$client = new AblyRest($config['key']);
            return new static();
        } catch (\Exception|\GuzzleHttp\Exception\GuzzleException $e) {
            throw new Exception("Could not connect to Ably: {$e->getMessage()}");
        }
    }

    public function publish(string|array $channels, string $event, array $payload = [], string $socketId = null): mixed
    {
        if (!static::$client instanceof AblyRest) {
            throw new Exception("Ably client not connected.");
        }

        $channels = (array)$channels;
        $results = [];
        foreach ($channels as $channelName) {
            try {
                $channel = static::$client->channels->get($channelName);
                $result = $channel->publish($event, $payload);
                $results[$channelName] = $result;
            } catch (\Exception $e) {
                $results[$channelName] = false;
            }
        }
        return count($results) === 1 ? reset($results) : $results;
    }

    /**
     * @throws Exception
     */
    public function listChannels(array $params = []): array//: PaginatedResult
    {
        $client = static::$client;
        if (!($client instanceof AblyRest)) {
            throw new Exception("Ably client not connected or invalid type.");
        }

        try {
            return $client->get("/channels");
        } catch (\Exception $e) {
            throw new Exception("Could not list Ably channels: {$e->getMessage()}");
        }
    }

    /**
     * @throws Exception
     */
    public function getPresenceMembers($channelName): array
    {
        $client = static::$client;
        if (!($client instanceof AblyRest)) {
            throw new Exception("Ably client not connected or invalid type.");
        }

        try {
            $presenceMembers = $client->channels->get('presence:' . $channelName)->presence->get();

            $membersData = [];

            foreach ($presenceMembers->items as $member) {

                $memberInfo = [
                    'clientId' => $member->clientId,
                    'action' => $member->action,
                    'timestamp' => $member->timestamp,
                    'user_data' => $member->data,
                ];
                $membersData[] = $memberInfo;
            }

            return $membersData;
        } catch (\Exception $e) {
            throw new Exception("Could not list Ably channels: {$e->getMessage()}");
        }
    }

}
