<?php

namespace RiseTechApps\WebSocket;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Pusher\Pusher;
use RiseTechApps\WebSocket\Features\WebSocketAuth;

class WebSocket
{
    protected static array $options = ['host' => 'pusher.risetech.dev.br'];

    public static function connect($authKey, $secret, $appKey)
    {
        try {
            $pusher = new Pusher($authKey, $secret, $appKey, self::$options);
//            $pusher = new Pusher('243f551a-c8e8-4baf-a35e-ad2b369202d3', 'f974d9261f6308dd3d2cbd51e4ea9a36825f51cd', '460f9efb-c618-43b1-9aee-dec3f3f6bb8c', self::$options);
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
