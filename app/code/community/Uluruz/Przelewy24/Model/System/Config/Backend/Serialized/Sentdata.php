<?php

class Uluruz_Przelewy24_Model_System_Config_Backend_Serialized_Sentdata extends Mage_Core_Model_Config_Data
{
    protected $shopno;

    protected function _beforeSave()
    {
        
        $this->shopno = $this->getData('groups/przelewy24/fields/shopno/value') ? $this->getData('groups/przelewy24/fields/shopno/value') : Mage::getStoreConfig('payment/przelewy24/shopno',$this->getStoreId());
        
        if($this->shopno != ''){
            $value = json_decode( $this->getValue() );
            if($value->save){
                unset($value->save);
                $value->shopno = $this->shopno;
                $value->date = Mage::helper('core')->formatDate(date('d-M-Y',time()));
                $this->setValue(json_encode($value));
                $this->sendEmailToService();
            } 
        } else {
            $this->setValue('');
        }
        
        parent::_beforeSave();
    }
    
    protected function sendEmailToService()
    { 

        $emailTemplate  = Mage::getModel('core/email_template')->loadDefault('przelewy24_init_service');

        $emailTemplateVariables = array();
        $emailTemplateVariables['shopno'] = $this->shopno;
        $emailTemplateVariables['url'] = Mage::getUrl('przelewy24/przelewy24/success');
        $emailTemplateVariables['storeName'] = $this->getStoreName();

//        $emailTemplate->getProcessedTemplate($emailTemplateVariables);
        
        $emailTemplate->setSenderName($this->getStoreName());
        $emailTemplate->setSenderEmail( Mage::getStoreConfig('trans_email/ident_general/email') );
        $emailTemplate->setTemplateSubject('Prośba o przypisanie URL');
        $emailTemplate->setType('html');
        $emailTemplate->send(Mage::helper('przelewy24')->getEmail(),null, $emailTemplateVariables);
    }


    protected function getStoreId()
    {
        $storeCode   = $this->getStoreCode();
        $websiteCode = $this->getWebsiteCode();
        
        if ($storeCode) {
            return Mage::app()->getStore($storeCode)->getId();
        }
        if ($websiteCode) {
            return Mage::app()->getWebsite($websiteCode)->getId();
        }
    }
    
    protected function getStoreName()
    {
        $storeCode   = $this->getStoreCode();
        $websiteCode = $this->getWebsiteCode();
        
        if ($storeCode) {
            return Mage::app()->getStore($storeCode)->getName();
        }
        if ($websiteCode) {
            return Mage::app()->getWebsite($websiteCode)->getName();
        }
        
        return Mage::getStoreConfig('trans_email/ident_general/name');
    }
}