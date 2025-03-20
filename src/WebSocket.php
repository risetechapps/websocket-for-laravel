<?php

namespace RiseTechApps\WebSocket;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Pusher\Pusher;
use RiseTechApps\WebSocket\Features\WebSocketAuth;

class WebSocket
{
    protected static array $options = [];

    public static function connect($authKey, $secret, $appKey): void
    {
        static::$options = [
            'host' => Config::get('websockets.host'),
        ];

        try {
            $pusher = new Pusher($authKey, $secret, $appKey, self::$options);
            $channels = $pusher->getChannels();
            $connections = config('broadcasting.connections');

            $connections['tenant'] = [
                'driver' => 'pusher',
                'key' => $authKey,
                'secret' => $secret,
                'app_id' => $appKey,
                'options' => self::$options
            ];

            Config::set('broadcasting.connections', $connections);
            Config::set('broadcasting.default', 'tenant');

        } catch (\Exception $e) {

        } catch (GuzzleException $e) {

        }
    }

    public function routes($options = []): void
    {
        Route::group($options, function () use ($options) {

            Route::any('/broadcasting/auth', function (Request $request) use ($options) {

                return WebSocketAuth::auth($request);
            });
        });
    }
}
