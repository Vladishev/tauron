<?php

class Tim_Crm_Model_Payment extends Mage_Core_Model_Abstract
{
    const BANK_TRANSFER = 'banktransfer';
    
    public function getCrmPaymentCode($code)
    {
        $paymentOptions = array(
            'przelewy24'        => 'S03',
            'payu'              => 'A00',
            'cashondelivery'    => 'POB',
            self::BANK_TRANSFER      => 'PRO'
        );
        
        return $paymentOptions[$code];
    }
}


