<?php

require_once __DIR__ . "/../vendor/autoload.php";

date_default_timezone_set('UTC');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if ($_ENV['APP_DEBUG'] === "true") {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

$paths = ['/'];
$isDevMode = $_ENV['APP_DEBUG'] === "true";

// the connection configuration
$dbParams = [
    'driver'   => 'pdo_mysql',
    'user'     => $_ENV['DB_USERNAME'],
    'password' => $_ENV['DB_PASSWORD'],
    'dbname'   => $_ENV['DB_NAME'],
];

$config = \Doctrine\ORM\ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);
$connection = \Doctrine\DBAL\DriverManager::getConnection($dbParams, $config);

$entityManager = new \Doctrine\ORM\EntityManager($connection, $config);

$builder = new \DI\ContainerBuilder();
//$builder->addDefinitions(__DIR__ . '/dependencies.php');
$container = $builder->build();