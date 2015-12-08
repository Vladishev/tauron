<?php
require_once(Mage::getModuleDir('controllers', 'Mage_Customer') . DS . 'AccountController.php');

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Tauron
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Tauron_Customer_AccountController extends Mage_Customer_AccountController
{
    /**
     * Deny customer login functionality
     */
    public function loginAction()
    {
        $this->_redirectUrl(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB));
    }
}