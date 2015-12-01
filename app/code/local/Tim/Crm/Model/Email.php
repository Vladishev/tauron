<?php

class Tim_Crm_Model_Email extends Mage_Core_Model_Abstract
{
    protected $_recipients = array(
        array('email' => 'j.kiernicki@tim.pl','name' => 'Jacek Kiernicki'),
        array('email' => 'k.drobnik@tim.pl','name' => 'Krzysztof Drobnik'),
        array('email' => 'm.buczak@tim.pl','name' => 'Marcin Buczak'),
        );
    
    public function sendError($message)
    {
        $emailTpl  = Mage::getModel('core/email_template')
            ->setDesignConfig(array('area' => 'frontend', 'store' => 2))
            ->loadDefault('crm_error_notification_template')
            ->setSenderName( Mage::getStoreConfig('trans_email/ident_general/name',2) )
            ->setSenderEmail( Mage::getStoreConfig('trans_email/ident_general/email',2) );

        $emailTplVars = array(
            'message'   => $message,
            'store_url' => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB,2)
        );

        foreach($this->_recipients as $recipient){
            $emailTpl->send( $recipient['email'], '', $emailTplVars ); 
        }
    }
}

