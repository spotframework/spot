<?php
namespace Spot\Module\Doctrine\DBAL;

use Doctrine\DBAL\DriverManager;
use Spot\Inject\Named;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;

class DoctrineDBALModule {
    /** @Provides("Doctrine\DBAL\Connection") @Singleton */
    static public function provideDBALConnection(
            /** @Named("doctrine.dbal.connection.host") */$host,
            /** @Named("doctrine.dbal.connection.driver") */$driver,
            /** @Named("doctrine.dbal.connection.dbname") */$dbname,
            /** @Named("doctrine.dbal.connection.username") */$username,
            /** @Named("doctrine.dbal.connection.password") */$password) {
        return DriverManager::getConnection([
            "host" => $host,
            "driver" => $driver,
            "dbname" => $dbname,
            "username" => $username,
            "password" => $password,
        ]);
    }
}
