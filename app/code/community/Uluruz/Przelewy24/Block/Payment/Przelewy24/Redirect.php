<?php

class Uluruz_Przelewy24_Block_Payment_Przelewy24_Redirect extends Mage_Core_Block_Abstract 
{

    protected function _toHtml() 
    {
    	$order_id = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
    	$order->addStatusToHistory(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, Mage::helper('przelewy24')->__('Waiting for payment.'));
        $order->sendNewOrderEmail();
        $order->save();
        $przelewy = Mage::getSingleton("przelewy24/payment_przelewy24");
		
        $form = new Varien_Data_Form();

        $form->setAction(rtrim(Mage::helper('przelewy24')->getParams()->url).'/')
             ->setId('przelewy24_przelewy24_checkout')
             ->setName('przelewy24_przelewy24_checkout')
             ->setMethod('POST')
             ->setUseContainer(true);

        foreach ($przelewy->getRedirectionFormData() as $field => $value) {
            $form->addField($field, 'hidden', array('name' => $field, 'value' => $value));
        }

        $html = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
				 </head><body>';
        $html.= 'Zostaniesz przekierowany na stronę serwisu platniczego Przelewy24';
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">document.getElementById("przelewy24_przelewy24_checkout").submit();</script>';
        $html.= '</body></html>';
        return $html;
    }
}