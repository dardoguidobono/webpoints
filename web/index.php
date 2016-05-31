<?php
/**
 * Bootstrapping
 */
use Core\Application;

require_once __DIR__ . '/../app/bootstrap.php';

/**
 * routes mount points
 */

$app->mount('/mobile',  new \Manager\Provider\MobileControllerProvider());
$app->get('/', function( Application $app ) {
    return "";
    //return $app->redirectToRoute( 'client.index' );
});



$app->run();