<?php

class Tim_Crm_Model_Webservice extends Mage_Core_Model_Abstract
{
    private $calledFunction;
    private $serverType;
    
    public function _runWebservice($data)
    {
        try{
            $soap = $this->soapConnection(debug_backtrace(false));
        } catch (Exception $e){
            Mage::getModel('crm/email')->sendError($e->getMessage());
            die;
        }

//        $func = next(debug_backtrace(false));
//        if($func['function'] == 'pushCustomerInfo'){
//            echo '<pre>';
//            print_r($data->getDatas());
//            die;
//        }
                $func = next(debug_backtrace(false));
        if($func['function'] == 'sendOrder'){
            echo '<pre>';
            print_r($data->getDatas());
            die;
        }
        try{
            $soap->__soapCall($this->getAction(), $data->getDatas());
            $data->setLastRequest( $soap->__getLastRequest() );
            $lastResponse = $soap->__getLastResponse();
            $data->setLastResponse( $lastResponse );
        } catch (Exception $e){
            Mage::getModel('crm/email')->sendError($e->getMessage());
            die;
        }
        
        return $lastResponse;
    }
    
    public function getCustomerInfo($data)
    {
        $data->prepareData();
        return $this->_runWebservice($data);      
    }
    
    public function pushCustomerInfo($data)
    {
        $data->prepareData();
        return $this->_runWebservice($data);      
    }
    
    public function sendOrder($data)
    {
        $data->prepareData();
        return $this->_runWebservice($data);
    }
    
    private function getHost()
    {
        $host = Mage::getStoreConfig('tim_crm/urls/'.$this->serverType.'_crm_url') ? 
                Mage::getStoreConfig('tim_crm/urls/'.$this->serverType.'_crm_url') : 
                Mage::getStoreConfig('tim_crm/urls/live_crm_url');
        
        return rtrim($host,'/').'/';
    }
    
    private function getCommandUri()
    {
         $commandUri = Mage::getStoreConfig('tim_crm/urls/'.$this->serverType.'_uri_'.$this->calledFunction) ?
                Mage::getStoreConfig('tim_crm/urls/'.$this->serverType.'_uri_'.$this->calledFunction) :
                Mage::getStoreConfig('tim_crm/urls/live_uri_'.$this->calledFunction);
         
         return trim($commandUri);
    }
    
    private function getAction()
    {
        $action = Mage::getStoreConfig('tim_crm/urls/'.$this->serverType.'_action_'.$this->calledFunction) ?
                Mage::getStoreConfig('tim_crm/urls/'.$this->serverType.'_action_'.$this->calledFunction) :
                Mage::getStoreConfig('tim_crm/urls/live_action_'.$this->calledFunction);
         
        // if action == null then send error e-mail
        if($action == null){
            Mage::getModel('crm/email')->sendError('Błąd wyznaczenia akcji WebSerwisu');
        }
        
        return trim($action);
    }
    
    public function soapConnection($debug_backtrace)
    {
        $debugBacktrace = next($debug_backtrace);
        $this->calledFunction = strtolower($debugBacktrace['function']);
        $this->serverType = trim(Mage::getStoreConfig('tim_local/server/type'));
        
        return new SoapClient($this->getHost().$this->getCommandUri(),array('trace' => 1, 'exceptions' => 1, "connection_timeout" => 15)); 
    }
}


