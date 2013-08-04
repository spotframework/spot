<?php
namespace Spot\Module\Doctrine\Orm;

use Spot\Spot;
use Spot\Inject\Named;
use Spot\Inject\Provides;
use Spot\Inject\Intercept;
use Spot\Inject\Singleton;
use Spot\Inject\Matcher\AnnotatedWith;
use Spot\Domain\Transactional;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;
use Doctrine\Common\Cache\Cache;


class DoctrineOrmModule {
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
            Configuration $config,
            /** @Named("app.dump-dir") */$dumpDir, 
            /** @Named("doctrine.orm") */$conn = null) {
        $conn = $conn ?: [
            "driver" => "pdo_sqlite",
            "path" => "{$dumpDir}/doctrine/orm/db.sqlite",
        ];
        
        return EntityManager::create($conn, $config);
    }
    
    /** @Provides(Provides::ELEMENT) @Transactional */
    static function provideUnitOfWork(DoctrineOrmUnitOfWork $work) {
        return $work;
    }
}