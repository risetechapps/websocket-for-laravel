<?php

namespace RiseTechApps\WebSocket;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use RiseTechApps\WebSocket\Contracts\WebSocketContract;
use RiseTechApps\WebSocket\Features\Driver\WebSocketAbly;
use RiseTechApps\WebSocket\Features\Driver\WebSocketPusher;
use RiseTechApps\WebSocket\Features\WebSocketAuth;

class WebSocket
{
    /**
     * @throws Exception
     * @throws GuzzleException
     */
    public static function connect(): WebSocketContract
    {
        $default = \config('broadcasting.default');
        $driver = \config('broadcasting.connections.' . $default . '.driver') ?? null;
        $config = \config('broadcasting.connections.' . $default) ?? [];

        return match ($driver) {
            'pusher' => WebSocketPusher::connect($config),
            'ably' => WebSocketAbly::connect($config),
            default => null
        };
    }

    public function routes($options = []): void
    {
        Route::group($options, function () use ($options) {

            Route::any('/broadcast', function (Request $request) use ($options) {

                return WebSocketAuth::auth($request);
            });
        });
    }
}
