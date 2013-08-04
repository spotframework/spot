<?php
namespace Spot\Module\Doctrine\MongoDB;

use Spot\Inject\Named;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;
use Spot\Domain\Transactional;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Cache\Cache;

class DoctrineMongoModule {
    /** @Provides("Doctrine\MongoDB\Connection") */
    static function provideConnection(
            /** @Named("mongo.host") */$host = "localhost",
            /** @Named("mongo.port") */$port = 27017,
            /** @Named("mongo.username") */$username = null,
            /** @Named("mongo.password") */$password = null) {
        $connString = "mongodb://";
        if($username) {
            $connString .= "{$username}:{$password}@";
        }
        
        $connString .= "{$host}:{$port}";
        
        return new Connection($connString);
    }

    /** @Provides("Doctrine\ODM\MongoDB\Configuration") */
    static function provideConfiguration(
            Cache $cache,
            /** @Named("app.debug") */$debug,
            /** @Named("app.dump-dir") */$dumpDir,
            /** @Named("app.module.paths") */array $paths,
            /** @Named("mongo.database") */$database = null) {
        $configuration = new Configuration();

        $configuration->setMetadataDriverImpl(AnnotationDriver::create($paths));
        $configuration->setMetadataCacheImpl($cache);
        
        $configuration->setHydratorDir("{$dumpDir}/doctrine/odm");
        $configuration->setHydratorNamespace("DoctrineGen");
        
        $configuration->setProxyDir("{$dumpDir}/doctrine/odm");
        $configuration->setProxyNamespace("DoctrineGen");
        
        $configuration->setAutoGenerateHydratorClasses($debug);
        $configuration->setAutoGenerateProxyClasses($debug);
        
        $database && $configuration->setDefaultDB($database);

        return $configuration;
    }

    /** @Provides("Doctrine\ODM\MongoDB\DocumentManager") @Singleton */
    static function provideDocumentManager(
            Connection $connection,
            Configuration $configuration) {
        return DocumentManager::create($connection, $configuration);
    }
    
    /** @Provides(Provides::ELEMENT) @Transactional */
    static function provideUnitOfWork(DoctrineMongoUnitOfWork $work) {
        return $work;
    }
}