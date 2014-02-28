<?php
namespace Spot\Module\React;

use Spot\Inject\Provides;
use Spot\Inject\Named;
use Spot\Inject\Singleton;
use React\EventLoop\LoopInterface;
use React\EventLoop\Factory;

class ReactModule {
    /** @Provides("giReact\EventLoop\LoopInterface") @Singleton */
    static public function provideEventLoop() {
        return Factory::create();
    }
}
