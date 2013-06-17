<?php
namespace Spot\Module\Monolog;

use Monolog\Logger;
use Spot\Inject\Named;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;
use Spot\Log\Impl\TwigMessageInterpolator;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\ChromePHPHandler;
use Monolog\Processor\PsrLogMessageProcessor;

class MonologModule {
    /** @Provides("Psr\Log\LoggerInterface") @Singleton */
    static function provideLogger(
            /** @Named("app.debug") */$debug,
            /** @Named("monolog.name") */$name = "",
            /** @Named("monolog.handlers") */array $handlers = [],
            /** @Named("monolog.processors") */array $processors = []) {        
        $uas = strtolower($_SERVER["HTTP_USER_AGENT"]);
        if($debug) {
            if(strpos($uas, "chrome")) {
                $handlers[] = new ChromePHPHandler();
            } else if(strpos($uas, "firefox")) {
                $handlers[] = new FirePHPHandler();
            }
        }
        
        $processors[] = new PsrLogMessageProcessor();
        
        return new Logger($name, $handlers, $processors);
    }
}