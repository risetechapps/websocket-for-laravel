<?php

namespace RiseTechApps\WebSocket\Features;

use Illuminate\Http\Request;
use Pusher\Pusher;
use Pusher\PusherException;

class WebSocketAuth
{
    protected static Pusher $connect;
    protected static string $channel;
    protected static string $socketId;

    /**
     * @throws PusherException
     */
    public static function auth(Request $request): string
    {
        self::$connect = self::connect();
        self::$channel = self::getChannel($request);
        self::$socketId = self::getSocketId($request);
        return self::generateToken();
    }

    /**
     * @throws PusherException
     */
    private static function connect(): Pusher
    {
        $default = config('broadcasting.default');

        $config = config('broadcasting.connections.' . $default);

        if ($config['driver'] == 'pusher') {
            return new Pusher(
                config("broadcasting.connections.${default}.key") ?? null,
                config("broadcasting.connections.${default}.secret") ?? null,
                config("broadcasting.connections.${default}.app_id") ?? null,
                $config['options'] ?? []
            );
        }
        return new Pusher("", "", "");
    }

    protected static function getChannel($request): ?string
    {
        return $request->input('channel_name') ?? null;
    }

    private static function getSocketId(Request $request)
    {
        return $request->input('socket_id');
    }

    /**
     * @throws PusherException
     */
    private static function generateToken(): string
    {
        return self::$connect->authorizePresenceChannel(self::$channel, self::$socketId, tenancy()->getTenantKey(), [
            'token' => auth()->user()->currentAccessToken()->token,
        ]);
    }
}
