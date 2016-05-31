<?php
/**
 * Main console program.
 * User: dardo
 * Date: 16/11/15
 * Time: 10:19
 */
use Console\Command\QueueGenerateSchemaCommand;
use Knp\Console\Application;

/**
* Bootstrapping
*/
require_once __DIR__ . '/../app/bootstrap.php';
/** @var Application $appConsole */
$appConsole = $app['console'];
$appConsole->run();