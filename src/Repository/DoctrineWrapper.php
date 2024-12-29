<?php

namespace App\Repository;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Logging\Middleware;
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

    public static function setup(): void
    {
        $isDevMode = Env::IS_LOCALHOST;
        $proxyDir = Env::BASE_PATH . "/tmp";
        $cache = null;
        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: array(__DIR__),
            isDevMode: $isDevMode,
            proxyDir: $proxyDir,
            cache: $cache,
        );
        $config->setAutoGenerateProxyClasses(true);

        # Log writing SQL queries
        $middleware = new Middleware(new Logger());
        $config->setMiddlewares([$middleware]);

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