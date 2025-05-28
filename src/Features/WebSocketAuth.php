<?php

namespace RiseTechApps\WebSocket\Features;

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
            // Validar entrada
            $channel = $request->input('channel_name');
            $socketId = $request->input('socket_id');

            if (empty($channel) || empty($socketId)) {
                return response()->json(['error' => 'channel_name and socket_id are required'], 400);
            }

            if (!auth()->check()) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            self::$connect = self::connect();
            self::$channel = $channel;
            self::$socketId = $socketId;

            $authResponse = self::generateToken();

            // Retorna JSON com a resposta para o frontend
            return response()->json(json_decode($authResponse));

        } catch (PusherException $e) {
            return response()->json(['error' => 'Pusher error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
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
