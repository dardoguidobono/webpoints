<?php

namespace Controller\Mobile;

use Config\Parameters;
use Entity\Webpoints\Point;
use Orm\DoctrineConnection;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MobileController
{


    /**
     * @param Application $app
     * @param Request $request
     * @return \Entity\User user
     */
    private function getUser( Application $app, Request $request ){
        $em = DoctrineConnection::get();
        $repository = $em->getRepository('\Entity\User');
        $user = $repository->findOneBy([
            'username' =>  $request->get('user'),
            'password' =>  sha1( $request->get('pwd') ),
        ]);
        return $user;
    }

    public function doAuthAction( Application $app , Request $req  )
    {
        try {
            $user = $this->getUser( $app, $req );
            if ( !$user ){
                return $app->json( ['status' => 'fail', 'reason' => 'Unauthorized'], 403 );
            }

            return $app->json( ['status' => 'ok' ] );
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return $app->json( ['status' => 'fail', 'reason' => 'Internal Fail'], 500 );
        }
    }

    public function doAddAction( Application $app ,Request $req  )
    {
        try {
            $user = $this->getUser( $app, $req );
            if ( !$user ){
                return $app->json( ['status' => 'fail', 'reason' => 'Unauthorized'], 403 );
            }

            $em = DoctrineConnection::get();
            $point = new Point();
            $point->setUser($user)
                ->setLatitude( $req->get('lat') )
                ->setLongitude($req->get('lng') );
            $em->persist($point);
            $em->flush();
            return $app->json( ['status' => 'ok' ] );
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return $app->json( ['status' => 'fail', 'reason' => 'Internal Fail'], 500 );
        }
    }


}