<?php
namespace Spot\Module\Doctrine\MongoDB;

use Doctrine\ODM\MongoDB\Configuration;
use Spot\Inject\Named;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;
use Spot\Domain\Transactional;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\DocumentManager;

class DoctrineMongoModule {
    /** @Provides("Doctrine\MongoDB\Connection") */
    static function provideConnection(
        /** @Named("mongo.host") */$host = "localhost",
        /** @Named("mongo.port") */$port = 27017,
        /** @Named("mongo.username") */$username = null,
        /** @Named("mongo.password") */$password = null,
        /** @Named("mongo.database") */$database = null) {
        return new Connection("mongodb://{$username}:{$password}@{$host}:{$port}/{$database}");
    }

    /** @Provides("Doctrine\ODM\MongoDB\Configuration") */
    static function provideConfiguration(
        /** @Named("doctrine.mongo") */$mongo = []) {
        $configuration = new Configuration();
        foreach($mongo as $key => $value) {
            if(method_exists($configuration, "set$key")) {
                $configuration->{"set$key"}($value);
            }
        }

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