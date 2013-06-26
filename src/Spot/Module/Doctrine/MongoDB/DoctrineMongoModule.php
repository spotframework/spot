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
        /** @Named("app.dump-dir") */$dumpDir,
        /** @Named("app.module.paths") */array $paths,
        /** @Named("doctrine.mongo") */$mongo = [],
        /** @Named("mongo.database") */$database) {
        $configuration = new Configuration();
        foreach($mongo as $key => $value) {
            if(method_exists($configuration, "set$key")) {
                $configuration->{"set$key"}($value);
            }
        }
        
        $configuration->setMetadataDriverImpl(AnnotationDriver::create($paths));

        $configuration->setHydratorDir("{$dumpDir}/DoctrineGen");
        $configuration->setHydratorNamespace("DoctrineGen");
        
        $configuration->setProxyDir("{$dumpDir}/DoctrineGen");
        $configuration->setProxyNamespace("DoctrineGen");
        
        $configuration->setDefaultDB($database);

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