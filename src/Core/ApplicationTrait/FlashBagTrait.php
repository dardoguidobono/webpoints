<?php
/**
 * Utility tool for flashbag & flash messages.
 * User: dardo
 * Date: 23/09/15
 * Time: 13:32
 */

namespace Core\ApplicationTrait;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

trait FlashBagTrait
{
    /**
     * @return FlashBagInterface
     */
    public function getFlashBag() {
        return $this['session']->getFlashBag();
    }

    /**
     * adds a warning session message
     * @param $message
     */
    public function addWarningMessage( $message ){
        $this->getFlashBag()->add('warning', $message);
    }

    /**
     * adds a info session message
     * @param $message
     */
    public function addInfoMessage( $message ){
        $this->getFlashBag()->add('info', $message);
    }

    /**
     * adds a success session message
     * @param $message
     */
    public function addSuccessMessage( $message ){
        $this->getFlashBag()->add('success', $message);
    }

    /**
     * adds a error session message
     * @param $message
     */
    public function addErrorMessage( $message ){
        $this->getFlashBag()->add('danger', $message);
    }
}
