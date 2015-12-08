<?php

class Tim_Crm_Model_Actions extends Mage_Core_Model_Abstract
{
    private function checkGuid($customer)
    {
        if($customer->getTimGuidCustomer() == '' || $customer->getTimGuidCustomer() == 0){ //should be getTimGuidCustomer //@todo
            $customer->setTimGuidCustomer( $this->generateGuid($customer) ); //@todo - change name
        }
    }
    
    protected function getSufix()
    {
        $serverType = trim(Mage::getStoreConfig('tim_local/server/type'));

        return Mage::getStoreConfig('tim_crm/suffix/'.$serverType.'_default') ?
               Mage::getStoreConfig('tim_crm/suffix/'.$serverType.'_suffix') :
               Mage::getStoreConfig('tim_crm/suffix/live_suffix');
    }
    
    private function generateGuid($customer)
    {
//        $guid = 223526920309;
        
        
        do {
            $guid = (string)(rand ( 100000000 , 999999999 )).((string)($this->getSufix())); 
            $data = Mage::getModel('crm/data_customer');
            $data->setCustomer($customer);
            $data->setGuid($guid);
            $response = Mage::getModel('crm/webservice')->getCustomerInfo($data);
            $data->setXml($response);
        } while( !$data->isError() );

        return $guid;
    }
    
    public function saveCustomer($order)
    {
        
        $customer = Mage::getModel('customer/customer')->load( $order->getCustomerId() );
        $this->checkGuid($customer);
        
        
        $data = Mage::getModel('crm/data_customer');
        
        $data->setCustomer($customer);
        
        $response = Mage::getModel('crm/webservice')->pushCustomerInfo($data);
        $data->setXml($response);
        
        if($data->isError()){
            date_default_timezone_set('CET');
            $order->setTimInfo( serialize(array_merge($this->getError($data),array('created_date' => date('d-m-Y H:i:s') )) ) );
            $order->setTimError(true);
            $order->save();
            return false;
        }
        
        $data->updateCustomer();
        $customer->save();
        
        return true;
    }
    
    public function saveOrder($order)
    {

        $customer = Mage::getModel('customer/customer')->load( $order->getCustomerId() );
        
        $data = Mage::getModel('crm/data');
        $data->setOrder($order)->setCustomer( $customer );
        
        $response = Mage::getModel('crm/webservice')->sendOrder($data);
        $data->setXml($response);
        
        $error = array();
        if($data->isError()){
            $error = $this->getError($data); 
            $order->setTimError(true);
        } else {
//            $this->sendAccountNumberToCustomerIfNecessary($order,$customer);
        }
        
        date_default_timezone_set('CET');
        
        $order->setTimInfo( serialize(array_merge($error,array('created_date' => date('d-m-Y H:i:s') )) ) );
        $data->updateOrder();
        
//        $order->save();

    }
    
    protected function getError($data)
    {
        return array(
                'message' => $data->getErrorMessage(), 
                'ws_request' => $data->getLastRequest(),
                'ws_response' => $data->getLastResponse(),
                );
    }
    
    public function sendAccountNumberToCustomerIfNecessary($order,$customer)
    {
        if($order->getPayment()->getMethodInstance()->getCode() == Tim_Crm_Model_Payment::BANK_TRANSFER)
        {
            
            $emailTpl  = Mage::getModel('core/email_template')
                ->setDesignConfig(array('area' => 'frontend', 'store' => $order->getStoreId()))
                ->loadDefault('bankaccount_number_template')
                ->setSenderName( Mage::getStoreConfig('trans_email/ident_general/name',$order->getStoreId()) )
                ->setSenderEmail( Mage::getStoreConfig('trans_email/ident_general/email',$order->getStoreId()) );

            $emailTplVars = array(
                'customer'  => $customer,
                'order'     => $order,
                'total'     => $order->getStore()->formatPrice( $order->getGrandTotal() ),
                'store_url' => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB,$order->getStoreId())
            );
            
            return $emailTpl->send( $order->getCustomerEmail(), $order->getStoreName(), $emailTplVars ); 
        }
    }
}


