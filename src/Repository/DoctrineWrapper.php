<?php

namespace App\Repository;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\ORMSetup;

use Env;
use Db;

class DoctrineWrapper
{
    private static EntityManager $entity_manager;

    public static DebugStack $logger;

    private function __construct(){}

    public static function manager(): EntityManager
    {
        return self::$entity_manager;
    }

    public static function dump(mixed $var): void
    {
        db::debug(Debug::dump($var,3,true, false));
    }

    public static function setup(): void
    {
        // Create a simple "default" Doctrine ORM configuration for Annotations
//        $isDevMode = Env::IS_LOCALHOST;
        $isDevMode = false;
        $proxyDir = Env::BASE_PATH . "/tmp";
        $cache = null;
        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: array(__DIR__),
            isDevMode: $isDevMode,
            proxyDir: $proxyDir,
            cache: $cache,
        );
        $config->setAutoGenerateProxyClasses(true);

        $connectionParams = [
            'dbname' => Env::DATABASE,
            'user' => Env::USER_NAME,
            'password' => Env::PASSWORD,
            'host' => Env::HOST_NAME,
            'driver' => 'pdo_mysql',
        ];
        $connection = DriverManager::getConnection(params: $connectionParams, config: $config);
        self::$entity_manager = new EntityManager($connection, $config);
    }
}