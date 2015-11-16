<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Tauron
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Tauron_Model_Observer
{
    public function closeAccess()
    {
        if (Mage::helper('tim_tauron')->checkGuest()) {
            if (Mage::helper('core/http')->getHttpReferer()) {
                $url = Mage::helper('core/http')->getHttpReferer() . '?popup=1';
            } else {
                $url = Mage::getUrl() . '?popup=1';
            }
            Mage::app()->getFrontController()->getResponse()->setRedirect($url);
            Mage::app()->getResponse()->sendResponse();
            exit;
        }
    }
}