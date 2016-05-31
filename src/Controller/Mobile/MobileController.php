<?php

namespace Controller\Mobile;

use Config\Parameters;
use Orm\DoctrineConnection;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MobileController
{


    /**
     * @param Application $app
     * @param Request $request
     * @return \Entity\Catalog\User user
     */
    private function getUser( Application $app, Request $request ){
        $em = DoctrineConnection::get();
        $repository = $em->getRepository('\Entity\Catalog\User');
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
            if ( $user->getPhoneIdentifier() == null ){
                // mobile set first time
                $user->setPhoneIdentifier( $req->get('phoneid') );
                $user->setPhoneNotificationToken( $req->get('phonetoken') );
                $user->setPhonePlatform( $req->get('phoneplatform') );



                $app['pushbots']->aliasData( $user->getPhonePlatform(), $user->getPhoneNotificationToken(), $user->getUsername() );
                $ret_alias = $app['pushbots']->setAlias();


                if ($ret_alias['status'] !='OK'){
                    $app['pushbots']->registerData( $user->getPhonePlatform(), $user->getPhoneNotificationToken(), $user->getUsername());
                    $app['pushbots']->registerDevice();
                }

                $em = DoctrineConnection::get();
                $em->flush();
                return $app->json( ['status' => 'ok' ] );
            }

            if ( $user->getPhoneIdentifier() !=  $req->get('phoneid') ){
                // invalid attempt to use another phone
                return $app->json( ['status' => 'fail', 'reason' => 'Duplicated Phone'], 403 );
            }
            return $app->json( ['status' => 'ok' ] );
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return $app->json( ['status' => 'fail', 'reason' => 'Internal Fail'], 500 );
        }
    }

    public function getOffersAction( Application $app ,Request $req  )
    {
        try {
            $user = $this->getUser( $app, $req );
            if ( !$user ){
                return $app->json( ['status' => 'fail', 'reason' => 'Unauthorized'], 403 );
            }
            if ( $user->getPhoneIdentifier() !=  $req->get('phoneid') ){
                // invalid attempt to use another phone
                return $app->json( ['status' => 'fail', 'reason' => 'Duplicated Phone'], 403 );
            }
            $em = DoctrineConnection::get();
            $query = $em->createQuery("SELECT o FROM \Entity\Catalog\Offer o");
            $results = [];
            foreach ( $query->getResult()  as $offer ){
                $results[] = $offer->toJson();
            }
            return $app->json( $results );
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return $app->json( ['status' => 'fail', 'reason' => 'Internal Fail'], 500 );
        }
    }


    public function getProductsAction( Application $app, Request $req )
    {
        try {
            $user = $this->getUser( $app, $req );
            if ( !$user ){
                return $app->json( ['status' => 'fail', 'reason' => 'Unauthorized'], 403 );
            }
            if ( $user->getPhoneIdentifier() !=  $req->get('phoneid') ){
                // invalid attempt to use another phone
                return $app->json( ['status' => 'fail', 'reason' => 'Duplicated Phone'], 403 );
            }
            $productCacheJson = '../' . Parameters::get('PRODUCTS_CACHE_FILE');
            if ( !file_exists( $productCacheJson )){
                throw new \RuntimeException('Could not Found product cache file' . $productCacheJson  );
            }

            return new Response( file_get_contents($productCacheJson) );
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return $app->json( ['status' => 'fail', 'reason' => 'Internal Fail'], 500 );
        }
    }


    public function getNotificationAction( Application $app, Request $req )
    {
        try {
            $em = DoctrineConnection::get();
            $em->beginTransaction();
            $results = [];
            foreach ($this->getNotifications( $req->get('lasttx', 0 ), $req->get('phoneid') ) as $not ){
                $results[] = $not->toJson();
                $not->setPhoneSync(true);
            }
            $em->flush();
            $em->commit();
            return $app->json( $results );

        } catch (Exception $ex) {
            $em->rollback();
            echo $ex->getMessage();
            return $app->json( ['status' => 'fail', 'reason' => 'Internal Fail'], 500 );
        }
    }

    /**
     * Obtains latest notification per user
     * @param integer $lastTx
     * @param string  $deviceIdentifier
     * @returns \Doctrine\Common\Collections\ArrayCollection|Notification[]
     */
    public static function getNotifications( $lastTx , $deviceIdentifier){
        $em = DoctrineConnection::get();
        $qb = $em->createQueryBuilder('n')
            ->select('n')
            ->from('\Entity\Catalog\Notification', 'n')
            ->join('n.user', 'u')
            ->where('n.id > :last_tx AND u.phone_identifier = :device_identifier AND n.phone_sync = false ')
            ->orderBy('n.id')
            ->setParameter( 'last_tx', $lastTx )
            ->setParameter( 'device_identifier', $deviceIdentifier );


        $query = $qb->getQuery();
        return $query->getResult();

    }

}