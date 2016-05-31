<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

include_once( __DIR__ . '/vendor/autoload.php' );
define('__SDIR__', __DIR__);

// replace with mechanism to retrieve EntityManager in your app
$entityManager = \Orm\DoctrineConnection::get();

return ConsoleRunner::createHelperSet($entityManager);