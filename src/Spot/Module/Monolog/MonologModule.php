<?php
namespace Spot\Module\Monolog;

use Monolog\Logger;
use Spot\Inject\Named;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;
use Spot\Log\Impl\TwigMessageInterpolator;

class MonologModule {
    /** @Provides("Psr\Log\LoggerInterface") @Singleton */
    static function provideLogger(
            /** @Named("monolog.name") */$name = "",
            /** @Named("monolog.handlers") */array $handlers = [],
            /** @Named("monolog.processors") */array $processors = []) {        
        return new Logger($name, $handlers, $processors);
    }
}