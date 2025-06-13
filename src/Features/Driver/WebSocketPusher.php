<?php

namespace RiseTechApps\WebSocket\Features\Driver;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Pusher\Pusher;
use RiseTechApps\WebSocket\Contracts\WebSocketContract;

class WebSocketPusher extends WebSocketContract
{
    /**
     * @throws Exception
     * @throws GuzzleException
     */
    public static function connect(array $config): static
    {
        try {
            static::$client = new Pusher($config['key'], $config['secret'], $config['app_id'], $config['options']);
            return new static();
        } catch (\Exception $e) {
            throw new Exception("Could not connect to Pusher: {$e->getMessage()}");
        } catch (GuzzleException $e) {
            throw new Exception("Could not connect to Pusher (Guzzle error): {$e->getMessage()}");
        }
    }


    /**
     * @throws Exception
     */
    public function publish(string|array $channels, string $event, array $payload = [], string $socketId = null): mixed
    {
        if (!static::$client instanceof Pusher) {
            throw new Exception("Pusher client not connected.");
        }

        // Pusher::trigger aceita string ou array de canais
        try {
            $result = static::$client->trigger($channels, $event, $payload, $socketId);
            return $result;
        } catch (\Exception $e) {
            throw new Exception("Failed to publish to Pusher channel(s): {$e->getMessage()}");
        }
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function listChannels(): array
    {
        $client = static::$client;
        if (!($client instanceof Pusher)) {
            throw new Exception("Pusher client not connected or invalid type.");
        }

        try {
            $response = $client->get('/channels', []);

            if (isset($response['channels']) && is_array($response['channels'])) {
                return array_keys($response['channels']);
            }
            return [];
        } catch (\Exception $e) {
            throw new Exception("Could not list Pusher channels: {$e->getMessage()}");
        }
    }

    /**
     * @throws Exception
     */
    public function getPresenceMembers($channelName): array
    {
        $client = static::$client;
        if (!($client instanceof Pusher)) {
            throw new Exception("Pusher client not connected or invalid type.");
        }

        if (!str_starts_with($channelName, 'presence-')) {
            throw new Exception("Channel '{$channelName}' is not a presence channel. Cannot get members.");
        }

        try {
            $response = $client->get_channel_info($channelName, ['info' => 'users']);

            $members = [];
            if (isset($response->users) && is_array($response->users)) {
                foreach ($response->users as $user) {
                    $members[] = [
                        'clientId' => $user->id,
                        'action' => 'present',
                        'timestamp' => round(microtime(true) * 1000),
                        'user_data' => property_exists($user, 'user_info') ? $user->user_info : null,
                    ];
                }
            }
            return $members;

        } catch (\Exception $e) {
            throw new Exception("Could not get Pusher channel members: {$e->getMessage()}");
        }
    }
}
