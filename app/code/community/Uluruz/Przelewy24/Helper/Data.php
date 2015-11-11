<?php
class Uluruz_Przelewy24_Helper_Data extends Mage_Core_Helper_Abstract
{
    const DIALCOM_SERVICE_EMAIL = 'serwis@przelewy24.pl';
    const URL_SERVICE_TEST = 'https://sandbox.przelewy24.pl';
    const URL_SERVICE_LIVE = 'https://secure.przelewy24.pl';
    
    protected $_params;
    
    public function getEmail()
    {
        return self::DIALCOM_SERVICE_EMAIL;
    }
    
    public function getParams()
    { 
        if($this->_params){
            return $this->_params;
        }
        $mode = Mage::getStoreConfig('payment/przelewy24/mode',Mage::app()->getStore()->getId());
        $modeTest = Mage::getStoreConfig('payment/przelewy24/modetest',Mage::app()->getStore()->getId());
        $Params = new stdClass();
        switch ($mode){
            case '0':
                $Params->isTest = true;
                $Params->test->isAnnonymous = true;
                $Params->test->isRegistered = false;
                $Params->url = self::URL_SERVICE_TEST;
                $Params->test->valid = true;
                $Params->test->fail = false;
                $Params->seller_id = '13224';
                if($modeTest == '1'){
                    $Params->test->valid = false;
                    $Params->test->fail = true;
                }
                break;
            case '1':
                $Params->isTest = true;
                $Params->test->isAnnonymous = false;
                $Params->test->isRegistered = true;
                $Params->url = self::URL_SERVICE_TEST;
                $Params->seller_id = 
                        Mage::getStoreConfig('payment/przelewy24/shopno',Mage::app()->getStore()->getId()) ? 
                        Mage::getStoreConfig('payment/przelewy24/shopno',Mage::app()->getStore()->getId()) : '13224';
                $Params->test->valid = true;
                $Params->test->fail = false;
                if($modeTest == '1'){
                    $Params->test->valid = false;
                    $Params->test->fail = true;
                }
                break;
            case '2':
                $Params->isTest = false;
                $Params->test->isAnnonymous = false;
                $Params->test->isRegistered = false;
                $Params->url = self::URL_SERVICE_LIVE;
                $Params->test->valid = true;
                $Params->test->fail = false;
                $Params->seller_id = Mage::getStoreConfig('payment/przelewy24/shopno',Mage::app()->getStore()->getId());
                break;
        }

        $this->_params = $Params;

        return $this->_params;
    }
    
    public function getCrcCode()
    {
        if($this->getParams()->isTest){
            return Mage::getStoreConfig('payment/przelewy24/crctest',Mage::app()->getStore()->getId());
        }
        return Mage::getStoreConfig('payment/przelewy24/crc',Mage::app()->getStore()->getId());
    }
    
    
} 