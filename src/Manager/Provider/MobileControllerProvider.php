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

        $controllers->match('/update','Controller\Mobile\MobileController::getProductsAction')
            ->bind('mobile.update');

        $controllers->match('/offers', 'Controller\Mobile\MobileController::getOffersAction')
            ->bind('mobile.offers');

        $controllers->match('/notifications', 'Controller\Mobile\MobileController::getNotificationAction')
            ->bind('mobile.notifications');

        return $controllers;
    }
}