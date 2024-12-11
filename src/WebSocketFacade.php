<?php

namespace RiseTechApps\WebSocket;

use Illuminate\Support\Facades\Facade;

/**
 * @see \RiseTechApps\WebSocket\Skeleton\SkeletonClass
 * @method routes($options = [])
 */
class WebSocketFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'websocket';
    }
}
