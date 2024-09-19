<?php

namespace Riconas\RiconasApi;

use Psr\Container\ContainerInterface;

return [
    'db-config' => require __DIR__ . '/../configs/db.config.php',
    'jwt-config' => require __DIR__ . '/../configs/jwt.config.php',
    \Riconas\RiconasApi\Integrations\Firebase\Jwt\JwtEncoder::class => function (ContainerInterface $c) {
        return new \Riconas\RiconasApi\Integrations\Firebase\Jwt\JwtEncoder($c->get('jwt-config'));
    },
    \Doctrine\ORM\EntityManager::class => function (ContainerInterface $c) {
        $dbConfigs = $c->get('db-config');
        $dbParams = [
            'driver' => 'pdo_mysql',
            'user' => $dbConfigs['username'],
            'password' => $dbConfigs['password'],
            'dbname' => $dbConfigs['database'],
        ];

        $config = \Doctrine\ORM\ORMSetup::createAttributeMetadataConfiguration(['/'], $_ENV['APP_DEBUG'] === "true");
        $connection = \Doctrine\DBAL\DriverManager::getConnection($dbParams, $config);

        return new \Doctrine\ORM\EntityManager($connection, $config);
    },
    Riconas\RiconasApi\Components\User\Repository\UserRepository::class => function (ContainerInterface $c) {
        return new Riconas\RiconasApi\Components\User\Repository\UserRepository(
            $c->get(\Doctrine\ORM\EntityManager::class),
        );
    },
    \Riconas\RiconasApi\Authentication\AuthenticationService::class => function (ContainerInterface $c) {
        return new \Riconas\RiconasApi\Authentication\AuthenticationService();
    }
];