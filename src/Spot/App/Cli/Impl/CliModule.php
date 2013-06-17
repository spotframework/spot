<?php
namespace Spot\App\Cli\Impl;

use Spot\Inject\Named;
use Spot\Inject\Provides;

class CliModule {
    /** @Provides @Named("app.cwd") */
    static function getCurrentWorkingDirectory() {
        return getcwd();
    }
}