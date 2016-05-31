<?php

/**
 * bootstrap.php
 * @author Dardo Guidobono <dardoguidobono@gmail.com>
 * @version 0.1
 * @since 20/07/2015
 */

use Bernard\Silex\BernardServiceProvider;
use Config\Parameters;
use DG\Silex\Form\Type\TypeaheadType;
use Knp\Provider\ConsoleServiceProvider;
use Manager\Registry\ManagerRegistry;
use Manager\ServiceProvider\PushBotsServiceProvider;
use Core\Application;
use Service\Catalog\GenerateCatalogFile;
use Service\Catalog\MailWorker;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;

use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

include_once(__DIR__ . '/../vendor/autoload.php');

define('__SDIR__', dirname(__DIR__));
date_default_timezone_set('UTC');

$app = new Application();
$app->register(new SessionServiceProvider());
$app->register(new UrlGeneratorServiceProvider());
$app->register(new SecurityServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new PushBotsServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new TranslationServiceProvider(), [ 'translator.messages' =>[] ] );
$app->register(new  SwiftmailerServiceProvider(),[
    'swiftmailer.options' => [
        'host' => Parameters::get('MAIL_SERVER'),
        'port' => Parameters::get('MAIL_PORT'),
        'username' => Parameters::get('MAIL_USERNAME'),
        'password' => Parameters::get('MAIL_PASSWORD'),
        'encryption' => Parameters::get('MAIL_ENCRYPTION'),
        'auth_mode' => Parameters::get('MAIL_AUTH_MODE'),
    ],
    'swiftmailer.use_spool' => false,
]);
/**
 * Console Service Provider
 **/
$app->register(new ConsoleServiceProvider(), [
    'console.name'              => 'ConsoleApp',
    'console.version'           => '1.0.0',
    'console.project_directory' => __DIR__ . '/..'
]);

$app->register(new DoctrineServiceProvider(), [
    'dbs.options' => [
        'main' => [
            'driver'   => Parameters::get("DB_DRIVER"),
            'dbname'   => Parameters::get("DB_DATABASE"),
            'user'     => Parameters::get("DB_USERNAME"),
            'password' => Parameters::get("DB_PASSWORD"),
            'host'     => Parameters::get("DB_HOSTNAME"),
            'charset'   => 'utf8',
        ],
        'bernard' => [
            'driver'   => Parameters::get("DB_DRIVER"),
            'dbname'   => Parameters::get("DB_DATABASE"),
            'user'     => Parameters::get("DB_USERNAME"),
            'password' => Parameters::get("DB_PASSWORD"),
            'host'     => Parameters::get("DB_HOSTNAME"),
            'charset'   => 'utf8',
        ],
    ]
]);
$app->register(new BernardServiceProvider(), [
    'bernard.options' => [
        'driver' => 'doctrine',
        'serializer' => 'simple',
    ],
    'bernard.receivers' => [
        'GenerateProductsFile' => 'worker.big_task_worker',
        'SendEmailClientMovements' => 'worker.mail_worker',
    ]

]);

$app['worker.big_task_worker'] = $app->share( function () use ($app) {
    return new GenerateCatalogFile( $app );
});
$app['worker.mail_worker'] = $app->share( function () use ($app) {
    return new MailWorker( $app );
});


/**
 * Form Extensions
 * Doctrine Brigde for form extension
 */
$app['form.extensions'] = $app->share(
        $app->extend('form.extensions', function ($extensions) use ($app) {
            $manager = new ManagerRegistry(
                null, [] , ['doctrine.em'], null, null, '\Doctrine\ORM\Proxy\Proxy'
            );
            $manager->setContainer( $app );
            $extensions[] = new DoctrineOrmExtension($manager);
            return $extensions;
            }
        )
    );
$app['form.types'] = $app->share(
    $app->extend('form.types', function ($types) use ($app) {
            $types[] = new TypeaheadType( $app, $app->getEntityManager() );
            return $types;
        }
    )
);

/**
 * Basic Config Options
 */
$app['locale'] = 'es';
$app['cache.path'] = __DIR__ . '/../cache';
$app['twig.options'] = [
                        'cache' => $app['cache.path'] . '/twig',
                        'strict_variables'=> true,
                        ];
$app['twig.path'] =  [ __DIR__ . '/../resources/views' ];
$app['twig.form.templates']  = [ 'Common/forms.html.twig' ];

$app['security.encoder.digest'] = $app->share( function () {
    return new MessageDigestPasswordEncoder('sha1', false, 1);
});

/**
 * Security
 */

$app['security.firewalls'] = [
    'admin_secured' => [
        'pattern' => '^/admin/',
        'form' => [ 'login_path' => '/login/admin', 'check_path' => '/admin/login_check'],
        'users' => [
            'admin' => [ 'ROLE_ADMIN', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3'],
        ],
        'logout' => [
            'logout_path' => '/admin/logout',
            'target' => '/',
            'invalidate_session' => true,
        ],
    ],
    'client_secured' => [
        'pattern' => '^/clients/',
        'form' => [ 'login_path' => '/login/client', 'check_path' => '/clients/login_check'],
        'users' => $app->share( function (){ return new \Manager\Provider\ClientUserProvider() ; } ),
        'logout' => [
            'logout_path' => '/clients/logout',
            'target' => '/',
            'invalidate_session' => true,
        ],
    ]
];

$app['resources_online'] = \Config\Parameters::get('RESOURCES_ONLINE');

if ( \Config\Parameters::get('ENVIRONMENT_PRODUCTION') ){
    $app['debug'] = false;
}else{
    $app['debug'] = true;
}

$app->after(function (Request $request, Response $response) {
    $response->headers->set('Access-Control-Allow-Origin', '*');
});
