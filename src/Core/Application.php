<?php
namespace Core;
use Core\ApplicationTrait\FlashBagTrait;
use Entity\Catalog\User;
use Orm\DoctrineConnection;
use \Silex\Application\FormTrait;
use Silex\Application\SecurityTrait;
use Silex\Application\TwigTrait;
use Silex\Application\UrlGeneratorTrait;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;


/**
 * Class Application
 * @package Core
 */
class Application extends \Silex\Application{
    use FormTrait;
    use TwigTrait;
    use SecurityTrait;
    use UrlGeneratorTrait;
    use FlashBagTrait;

    /**
     * Instantiate a new Application.
     *
     * Objects and parameters can be passed as argument to the constructor.
     *
     * @param array $values The parameters or objects.
     */
    public function __construct(array $values = array()){
        parent::__construct($values);

        $this['doctrine.em'] = $this->share(function () {
            return DoctrineConnection::get();
        });
    }

    /**
     * redirect to a specific route
     * @param string $route
     * @return string url generated from route
     */
    public function redirectToRoute( $route ){
        return ( $this->redirect( $this->url( $route ) ) );
    }

    /**
     * gets Doctrine Entity Manager
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager(){
        return $this['doctrine.em'];
    }

    /**
     * gets the auth token
     * @return TokenInterface
     */
    public function getToken(){
        return $this['security']->getToken();
    }

    /**
     * gets Current logged user
     * @return User|null
     */
    public function getUser(){
        $token = $this->getToken();
        if ($token){
            return $token->getUser();
        }else{
            return null;
        }
    }

}