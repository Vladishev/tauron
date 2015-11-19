<?php

class Tim_Crm_Model_Cron extends Mage_Core_Model_Abstract
{
    static public function run()
    {
        if(!Mage::getStoreConfig('tim_crm/cron/enable')){
            return;
        }

        $collection = Mage::getModel('sales/order')
                ->getCollection()
                ->addFieldToFilter('tim_sent_to_crm',array('eq' => false));
        
        if($collection->count()){
            foreach($collection as $order){
                if(Mage::getModel('crm/actions')->saveCustomer($order)){
                    Mage::getModel('crm/actions')->saveOrder($order);
                }
                $order->setTimSentToCrm(true);
                $order->save();
                $order_id = $order->getId();
                unset($order);
                
                /* temporary - JK */
                $order = Mage::getModel('sales/order')->load($order_id);
                $log = 'order_id: '.$order->getIncrementId().', tim_sent_to_crm: '.((string)$order->getTimSentToCrm());
                Mage::log($log, 'crmOrder.log');
                unset($order);
                /* */
            }
        }
    }
}


