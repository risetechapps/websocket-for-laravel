<?php

namespace RiseTechApps\WebSocket\Features;

use Ably\AblyRest;
use Illuminate\Http\Request;
use Pusher\Pusher;
use Pusher\PusherException;
use Illuminate\Http\JsonResponse;

class WebSocketAuth
{
    protected static Pusher $connect;
    protected static string $channel;
    protected static string $socketId;

    public static function auth(Request $request): JsonResponse
    {
        try {

            $user = auth()->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $ablyApiKey = config('broadcasting.connections.ably.key');
            $ably = new AblyRest($ablyApiKey);

            $clientId = $request->input('clientId', (string)$user->getKey());
            $channelName = $request->input('channelName');
            $capability = ['subscribe', 'presence', 'publish'];

            if ($channelName) {
                $capability = [$channelName => ['subscribe', 'presence']];
                if (str_starts_with($channelName, 'presence-')) {
                    $capability[$channelName][] = 'publish';
                }
            } else {
                $capability = ['*' => ['subscribe', 'presence', 'publish']];
            }

            try {
                $tokenRequest = $ably->auth->createTokenRequest([
                    'clientId' => (string)$clientId,
                    'capability' => $capability,
                    'timestamp' => $request->input('timestamp'),
                    'nonce' => $request->input('nonce'),
                ]);

                return response()->json($tokenRequest->toArray());
            } catch (\Exception $e) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

        } catch (PusherException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    private static function connect(): Pusher
    {
        $default = config('broadcasting.default');
        $config = config("broadcasting.connections.{$default}");

        if ($config['driver'] === 'pusher') {
            return new Pusher(
                $config['key'] ?? '',
                $config['secret'] ?? '',
                $config['app_id'] ?? '',
                $config['options'] ?? []
            );
        }

        // fallback vazio para evitar erro
        return new Pusher("", "", "");
    }

    private static function generateToken(): string
    {
        $user = auth()->user();
        $tenantKey = function_exists('tenancy') ? tenancy()->getTenantKey() : null;

        return self::$connect->authorizePresenceChannel(
            self::$channel,
            self::$socketId,
            $tenantKey,
            [
                'token' => $user->currentAccessToken()->token,
            ]
        );
    }
}
