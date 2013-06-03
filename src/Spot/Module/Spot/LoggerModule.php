<?php
namespace Spot\Module\Spot;

use Spot\Inject\Provides;
use Spot\Inject\Singleton;
use Spot\Inject\Intercept;
use Spot\Inject\Matcher\AnnotatedWith;
use Spot\Log\Impl\LoggerInterceptor;
use Spot\Log\Log;
use Monolog\Logger;
use Monolog\Handler\ChromePHPHandler;
use Monolog\Formatter\ChromePHPFormatter;
use Monolog\Processor\PsrLogMessageProcessor;

class LoggerModule {    
    /**
     * @Intercept(@AnnotatedWith("Spot\Log\Log"))
     * 
     * @Provides("Spot\Log\Impl\LoggerInterceptor") @Singleton
     */
    static function provideLoggerInterceptor(LoggerInterceptor $i) {
        return $i;
    }
}