<?php
namespace Spot\App\Cli\Impl;

use Spot\Inject\Provides;

class CliAppModule {
    /** @Provides("Spot\App\Cli\CliApp") */
    static function provideCliApp(CliAppImpl $cliApp) {
        return $cliApp;
    }
}