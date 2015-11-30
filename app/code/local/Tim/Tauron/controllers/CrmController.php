<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Tauron
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Tauron_CrmController extends Mage_Core_Controller_Front_Action
{
    /**
     * Copy method from app/code/local/Tim/Crm/controllers/Adminhtml/ManualController.php
     * @throws Exception
     */
    public function sendtocrmAction()
    {
        $orderId = (int)$this->getRequest()->getParam('id',false);
        $order = Mage::getModel('sales/order')->load($orderId);
        $order->setTimSentToCrm(false);
        $order->setTimError(false);
        $order->setTimInfo('');
        $order->save();

        if(!Mage::getStoreConfig('tim_crm/cron/enable')){
            if(Mage::getModel('crm/actions')->saveCustomer($order)){
                Mage::getModel('crm/actions')->saveOrder($order);
            }
            $order->setTimSentToCrm(true);
        }
        $order->save();
        $this->_redirectReferer();
    }
}