<?php

namespace App\Repository;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

use Env;

class DoctrineWrapper
{
    private static EntityManager $entity_manager;

    private function __construct(){}

    public static function manager(): EntityManager
    {
        return self::$entity_manager;
    }

    public static function dump(mixed $var): void
    {
        echo "CHeckME";
//        db::debug(Debug::dump($var,3,true, false));
    }

    public static function setup(): void
    {
        // Create a simple "default" Doctrine ORM configuration for Annotations
//        $isDevMode = Env::IS_LOCALHOST;
//        $isDevMode = false;
//        $proxyDir = Env::BASE_PATH . "/tmp";
//        $cache = null;
//        $config = ORMSetup::createAttributeMetadataConfiguration(
//            array(__DIR__),
//            $isDevMode,
//            $proxyDir,
//            $cache,
//        );
//
//        self::$logger = new DebugStack();
//
//        $config->setAutoGenerateProxyClasses(true);
//        $config->setSQLLogger(self::$logger);

        // database configuration parameters
//        $connectionParams = [
//            'dbname' => ,
//            'user' => Env::USER_NAME,
//            'password' => ,
//            'host' => Env::HOST_NAME,
//            'driver' => 'pdo_mysql',
//        ];
        $dbParams = [
            'driver'   => 'pdo_mysql',
            'host'     => Env::HOST_NAME,
            'user'     => Env::USER_NAME,
            'password' => Env::PASSWORD,
            'dbname'   => Env::DATABASE,
        ];
        $paths = ["src/Entity"];
        $isDevMode = Env::IS_LOCALHOST;
        $config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);
        $connection = DriverManager::getConnection($dbParams, $config);
        self::$entity_manager = new EntityManager($connection, $config);
    }
}