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
    Components\User\Repository\UserRepository::class => function (ContainerInterface $c) {
        return new Components\User\Repository\UserRepository(
            $c->get(\Doctrine\ORM\EntityManager::class),
        );
    },
    Components\PasswordResetRequest\Repository\PasswordResetRequestRepository::class => function (ContainerInterface $c) {
        return new Components\PasswordResetRequest\Repository\PasswordResetRequestRepository(
            $c->get(\Doctrine\ORM\EntityManager::class),
        );
    },
    Components\PasswordResetRequest\Service\PasswordResetRequestService::class => function (ContainerInterface $c) {
        return new Components\PasswordResetRequest\Service\PasswordResetRequestService(
            $c->get(\Doctrine\ORM\EntityManager::class),
        );
    },
    Authentication\AuthenticationService::class => function (ContainerInterface $c) {
        return new Authentication\AuthenticationService(
            $c->get(Integrations\Firebase\Jwt\JwtEncoder::class),
        );
    }
];