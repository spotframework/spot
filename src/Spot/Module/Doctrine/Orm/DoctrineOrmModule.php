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


class DoctrineOrmModule {
    /** @Provides("Doctrine\ORM\Configuration") */
    static function provideConfiguration(
            /** @Named("app.mode") */$mode) {
        return Setup::createAnnotationMetadataConfiguration(["/"], $mode === Spot::DEV_MODE);
    }
    
    /** @Provides("Doctrine\ORM\EntityManager") @Singleton */
    static function provideEntityManager(
            Configuration $config,
            /** @Named("doctrine.orm") */$conn = []) {
        $conn = $conn ?: [
            "driver" => "pdo_sqlite",
            "path" => realpath(sys_get_temp_dir())."/doctrine.sqlite",
        ];
        
        return EntityManager::create($conn, $config);
    }
    
    /** @Provides(Provides::ELEMENT) @Transactional */
    static function provideUnitOfWork(DoctrineOrmUnitOfWork $work) {
        return $work;
    }
}