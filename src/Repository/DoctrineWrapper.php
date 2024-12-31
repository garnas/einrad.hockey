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
        $isDevMode = ENV::IS_LOCALHOST;

        $config = new Configuration();

        $driverImpl = new AttributeDriver(paths: [Env::BASE_PATH . "/src/Entity"]);
        $config->setMetadataDriverImpl($driverImpl);

        $queryCache = ($isDevMode ? (new ArrayAdapter()) : (new PhpFilesAdapter("doctrine_queries")));
        $config->setQueryCache($queryCache);

        $metadataCache = ($isDevMode ? (new ArrayAdapter()) : (new PhpFilesAdapter("doctrine_metadata")));
        $config->setMetadataCache($metadataCache);

        $config->setProxyDir(Env::BASE_PATH . "/tmp");
        $config->setProxyNamespace("App\Proxies");

        $config->setAutoGenerateProxyClasses($isDevMode);

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