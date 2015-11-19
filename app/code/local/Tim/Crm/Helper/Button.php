<?php

class Tim_Crm_Helper_Button extends Mage_Core_Helper_Abstract
{
    public function getButtonData()
    {
        return array(
            'label'   => $this->__('Send to CRM'), 
            'onclick' => 'location.href=\''.Mage::getModel('adminhtml/url')->getUrl('crm_admin/adminhtml_manual/sendtocrm', array('id' => $this->_getOrderId ())).'\';',
            );
    }

    protected function _getOrderId()
    {
        return Mage::registry('current_order')->getId();
    }
    
    public function getButtonArea()
    {
        $orderId = $this->_getOrderId();
        $order = Mage::getModel('sales/order')->load($orderId);
        
        if($order->getTimChanceId()){
            return 'hidden';
        } else if(Mage::getStoreConfig('tim_crm/cron/enable') && !$order->getTimError()){
            return 'hidden';
        }
        
        return 'header';
    }
    
}


