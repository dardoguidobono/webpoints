<?php
/*
* DoctrineConnection.php
* @author Dardo Guidobono <dardoguidobono@gmail.com>
* @version 0.1
* @since 09/06/2015
*/
namespace Orm;
use \Config\Parameters as Parameters;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

class DoctrineConnection  {


    /**
     * The instance of the DoctrineConnection
     *
     * @var object
     */
    protected static $instance = null;

    /**
     * Gets an DoctrineConnection instance
     * @parameter boolean $newInstance if a new instance must be created
     * @return \Doctrine\ORM\EntityManager $singleton
     */
    public static function get( $newInstance = false )
    {
        if ( $newInstance ){
            self::$instance = null;
        }

        // if closed create a new one
        if ( (self::$instance) && ( !self::$instance->isOpen() ) ) {
            self::$instance = null;
        }

        if ( !self::$instance ) {
            $config = new \Doctrine\ORM\Configuration();
            $config->addCustomStringFunction('SHA1', 'DoctrineExtensions\Query\Mysql\Sha1');

            if ( Parameters::get("APC_ENABLED" ) ) {
                $config->setMetadataCacheImpl( new \Doctrine\Common\Cache\ApcCache );
            }else{
                $config->setMetadataCacheImpl( new \Doctrine\Common\Cache\ArrayCache );
            }

            $driverImpl = new AnnotationDriver(new AnnotationReader(), array( __SDIR__ . "/src/Entity"));
            // registering noop annotation autoloader - allow all annotations by default
            AnnotationRegistry::registerLoader('class_exists');
            $config->setMetadataDriverImpl( $driverImpl );

            $config->setProxyDir( __SDIR__ . '/cache/Proxies' );
            $config->setProxyNamespace('Proxies');

            $connectionOptions = array(
                'dbname'   => Parameters::get("DB_DATABASE"),
                'user'     => Parameters::get("DB_USERNAME"),
                'password' => Parameters::get("DB_PASSWORD"),
                'host'     => Parameters::get("DB_HOSTNAME"),
                'driver'   => Parameters::get("DB_DRIVER"),

            );
            $em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);
            $em->getEventManager()->addEventSubscriber(new \Doctrine\DBAL\Event\Listeners\MysqlSessionInit('utf8', 'utf8_unicode_ci'));
            $em->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

            new \Symfony\Component\Console\Helper\HelperSet(array(
                'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
                'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
            ));
            self::$instance = $em;
        }
        return self::$instance;
    }


}
