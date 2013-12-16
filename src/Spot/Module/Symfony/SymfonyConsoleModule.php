<?php
namespace Spot\Module\Symfony;

use Spot\Inject\Injector;
use Spot\Inject\Named;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;
use Spot\Reflect\Match;
use Spot\Reflect\Reflection;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;

class SymfonyConsoleModule {
    /** @Provides("Symfony\Component\Console\Application") @Singleton */
    static function provideApp(
            /** @Named("symfony.console.name") */$name = "UNKNOWN",
            /** @Named("symfony.console.version") */$version = "UNKNOWN",
            /** @Named("symfony.console.helpers") */$helpers = [],
            /** @Named("symfony.console.commands") */$commands = []) {
        $app = new Application($name, $version);
        $app->addCommands($commands);

        $helperSet = $app->getHelperSet();
        foreach($helpers as $alias => $helper) {
            $helperSet->set($helper, $alias);
        }

        return $app;
    }

    /** @Provides @Named("symfony.console.commands") */
    static function provideCommands(
            Injector $injector,
            Reflection $reflection,
            /** @Named("app.module.namespaces") */array $namespaces = [],
            /** @Named("symfony.console.commands") */array $commands = [],
            /** @Named("symfony.console.command-groups") */array $groups = []) {
        $matcher = Match::subtypeOf("Symfony\\Component\\Console\\Command\\Command");
        foreach($namespaces as $ns) {
            foreach($reflection->find($ns, $matcher) as $type) {
                $commands[] = $injector->getInstance($type->name);
            }
        }

        foreach($groups as $groupedCommands) {
            $commands = array_merge($commands, $groupedCommands);
        }

        return $commands;
    }

    /** @Provides @Named("symfony.console.helpers") */
    static function provideHelperSet(
            /** @Named("symfony.console.helpers") */array $helpers = [],
            /** @Named("symfony.console.helper-groups") */array $groups = []) {
        foreach($groups as $groupedHelpers) {
            $helpers += $groupedHelpers;
        }

        return $helpers;
    }
}
