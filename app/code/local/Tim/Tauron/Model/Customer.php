<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Tauron
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Tauron_Model_Customer extends Mage_Customer_Model_Customer
{
    /**
     * Disabled Magento Default Welcome Message
     * @param string $type
     * @param string $backUrl
     * @param string $storeId
     * @return $this
     */
    public function sendNewAccountEmail($type = 'registered', $backUrl = '', $storeId = '0')
    {
        return $this;
    }
}