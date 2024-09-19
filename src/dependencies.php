<?php

use Psr\Container\ContainerInterface;

return [
    \Doctrine\ORM\EntityManager::class => function (ContainerInterface $c) {
        $dbParams = [
            'driver'   => 'pdo_mysql',
            'user'     => $_ENV['DB_USERNAME'],
            'password' => $_ENV['DB_PASSWORD'],
            'dbname'   => $_ENV['DB_NAME'],
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