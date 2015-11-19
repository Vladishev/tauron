<?php

abstract class Tim_Crm_Model_Data_Abstract extends Mage_Core_Model_Abstract
{
    
    const RECOGNIZED_NAMESPACE = 'http://www.openapplications.org/oagis/9';
    
    protected $soapAreas;
    protected $error = array();
    
    protected $data;
    protected $customer;
    protected $crmReceivedData;
    protected $lastRequest;
    protected $lastResponse;
    
        
    abstract public function isError();
    
    public function setXml($xmlstring)
    {
        $xml = simplexml_load_string ($xmlstring);
        $ns = $xml->getNamespaces(true);
        $acceptedNS = '';
        $accepted = false;
        if (!empty($ns)) foreach($ns as $nsID => $nsURI) {
            if ($nsURI == self::RECOGNIZED_NAMESPACE) {
                $accepted = true;
                $acceptedNS = $nsURI;
            }
        }
        if (!$accepted) {
            //error here
            die;
        }
        $soapData = $xml->children($ns['s']);
        $syncPartyMaster = $soapData->Body->children($acceptedNS);
        
        $areas = array();
        foreach($syncPartyMaster->children($acceptedNS) as $area) {
            $areas[$area->getName()] = $area;
        }
        
        $this->soapAreas = $areas;
        return $this;
    }
    
    public function setLastRequest($lastRequest)
    {
        $this->lastRequest = $lastRequest;
        return $this;
    }
    
    public function getLastRequest()
    {
        return $this->lastRequest;
    }
    
    public function setLastResponse($lastResponse)
    {
        $this->lastResponse = $lastResponse;
        return $this;
    }
    
    public function getLastResponse()
    {
        return $this->lastResponse;
    }
    
    public function getDatas()
    {
        return $this->data;
    }
    
    public function getErrorMessage()
    {
        if(count($this->error) == 0){
            if(!$this->isError()){
                return '';
            }
        }
        
        return $this->error['message'];
    }
    
    protected function getSoapAreas()
    {
        return $this->soapAreas;    
    }
    
    public function prepareData()
    {
        $debugBacktrace = next(debug_backtrace(false));
        $structure = $this->getStructure( $debugBacktrace['function'] );
        
        return $this->data = $this->createData($structure);
    }
    
    protected function createData($structure)
    {
        if(is_array($structure)){
            foreach($structure as $key => $str){
                /* recurencive */
                $structure[$key] = $this->createData($str);
            }
            return $structure;
        }
        
        $explodedStringA = explode(' ',$structure);
        if(current($explodedStringA) == 'func'){
            $explodedStringB = explode(';',next($explodedStringA));
            
            /* MAGIC: call function here :) */
            return $this->createData( $this->{ current($explodedStringB) }( next($explodedStringB) ) ); 
        }
        
        return $structure;
    }
    
    public function getCustomer()
    {
        return $this->customer;
    }
    
    public function setCustomer($customer)
    {
        $this->customer = $customer;
        return $this;
    }
    
    protected function getStructure($calledFunction)
    { 
        return $this->{'getStructureOf'.  ucfirst($calledFunction)}();  
    }
    
    protected function getApplicationArea() //wspólny
    {
        return array (
            'Sender' => array ('LogicalID' => Mage::getStoreConfig('tim_crm/settings/logical_id',$this->getCustomer()->getWebsiteId())),
            'Receiver' => array ('LogicalID' => 'CRM'),
            'CreationDateTime' => date('Y-m-d H:i:s'),
            'BODID' => Mage::getStoreConfig('tim_crm/settings/body_id',$this->getCustomer()->getWebsiteId()),
        );
    }
    
    protected function getSync($param = false)
    {
        return array('ActionCriteria' =>
                        array('ActionExpression' =>
                            $param == 'sendOrder' ? array('_' => 'Finish','actionCode' => 'Cart') : array('actionCode' => 'Sync')
                        )
                    );
    }
    
}


