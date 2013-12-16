<?php
namespace Spot\Module;

use Spot\Inject\Named;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;

class MongoClientModule {
    /** @Provides("MongoClient") @Singleton */
    static function provideMongoClient(
            /** @Named("mongo.host") */$host = "localhost",
            /** @Named("mongo.port") */$port = 27017,
            /** @Named("mongo.user") */$user = null,
            /** @Named("mongo.password") */$password = null) {
        $dsn = "mongodb://";
        if($user && $password) {
            $dsn .= "{$user}:{$password}@";
        }

        $dsn .= "{$host}:{$port}";

        return new \MongoClient($dsn);
    }

    /** @Provides("MongoDB") */
    static function provideMongoDB(
            \MongoClient $client,
            /** @Named("mongo.db") */$db) {
        return $client->selectDB($db);
    }
}
