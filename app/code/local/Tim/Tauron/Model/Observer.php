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
    /**
     * Sends order id to crm module
     * @param Varien_Event_Observer $observer
     */
    public function sendOrderId(Varien_Event_Observer $observer)
    {
        $orderId = $observer->getEvent()->getOrder()->getId();
        $url = Mage::getUrl('tim_tauron/crm/sendtocrm') . '?id=' . $orderId;
        header( 'Location: ' . $url );
    }
}