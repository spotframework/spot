<?php
namespace Spot\Module\Doctrine\ORM;

use Spot\Inject\Named;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;
use Spot\Domain\Transactional;
use Doctrine\Common\Cache\Cache;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\ORM\Tools\Setup;
use Symfony\Component\Console\Application;

class DoctrineORMModule {
    /** @Provides("Doctrine\ORM\Configuration") */
    static function provideConfiguration(
        Cache $cache,
        /** @Named("app.debug") */$debug,
        /** @Named("app.dump-dir") */$dumpDir,
        /** @Named("app.module.paths") */array $paths = []) {
        return Setup::createAnnotationMetadataConfiguration(
            $paths,
            $debug,
            "{$dumpDir}/doctrine/orm",
            $cache
        );
    }

    /** @Provides("Doctrine\ORM\EntityManager") @Singleton */
    static function provideEntityManager(
            Connection $connection,
            Configuration $config) {
        return EntityManager::create($connection, $config);
    }

    /** @Provides("Doctrine\DBAL\Connection") @Singleton */
    static function provideConnection(
            /** @Named("doctrine.dbal") */$params = null) {
        $params = $params ?: [
            "driver" => "pdo_sqlite",
            "path" => sys_get_temp_dir(). "/db.sqlite",
        ];

        $conn = DriverManager::getConnection($params);
        $platform = $conn->getDatabasePlatform();
        if(isset($params["mapping-types"])) {
            foreach($params["mapping-types"] as $dbType => $doctrineType) {
                $platform->registerDoctrineTypeMapping($dbType, $doctrineType);
            }
        }

        return $conn;
    }

    /** @Provides(Provides::ELEMENT) @Transactional */
    static function provideUnitOfWork(DoctrineORMUnitOfWork $work) {
        return $work;
    }

    /** @Provides(Provides::ELEMENT) @Named("symfony.console.command-groups") @Singleton */
    static public function provideCommandGroup() {
        return [
            // DBAL Commands
            new \Doctrine\DBAL\Tools\Console\Command\RunSqlCommand(),
            new \Doctrine\DBAL\Tools\Console\Command\ImportCommand(),

            // ORM Commands
            new \Doctrine\ORM\Tools\Console\Command\ClearCache\MetadataCommand(),
            new \Doctrine\ORM\Tools\Console\Command\ClearCache\ResultCommand(),
            new \Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand(),
            new \Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand(),
            new \Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand(),
            new \Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand(),
            new \Doctrine\ORM\Tools\Console\Command\EnsureProductionSettingsCommand(),
            new \Doctrine\ORM\Tools\Console\Command\ConvertDoctrine1SchemaCommand(),
            new \Doctrine\ORM\Tools\Console\Command\GenerateRepositoriesCommand(),
            new \Doctrine\ORM\Tools\Console\Command\GenerateEntitiesCommand(),
            new \Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand(),
            new \Doctrine\ORM\Tools\Console\Command\ConvertMappingCommand(),
            new \Doctrine\ORM\Tools\Console\Command\RunDqlCommand(),
            new \Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand(),
            new \Doctrine\ORM\Tools\Console\Command\InfoCommand()
        ];
    }

    /** @Provides(Provides::ELEMENT) @Named("symfony.console.helper-groups") @Singleton */
    static public function provideHelpers(
            EntityManager $em,
            Connection $connection) {
        return [
            "em" => new EntityManagerHelper($em),
            "db" => new ConnectionHelper($connection),
        ];
    }
}
