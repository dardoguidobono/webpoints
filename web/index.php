<?php
/**
 * Bootstrapping
 */
use Core\Application;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/../app/bootstrap.php';

/**
 * routes mount points
 */

$app->mount('/mobile',  new \Manager\Provider\MobileControllerProvider());
$app->get('/', function( Application $app ) {
    return "";
    //return $app->redirectToRoute( 'client.index' );
});

$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});


$app->run();