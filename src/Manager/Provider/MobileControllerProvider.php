<?php
namespace Manager\Provider;
use Silex\Application;
use Silex\ControllerProviderInterface;
class MobileControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->match('/auth', 'Controller\Mobile\MobileController::doAuthAction')
            ->bind('mobile.auth');

        $controllers->match('/add', 'Controller\Mobile\MobileController::doAddAction')
            ->bind('mobile.add');

        return $controllers;
    }
}