<?php
class Tim_Tauron_Block_Checkout_Onepage extends Mage_Checkout_Block_Onepage
{
    public function getSteps()
    {
        $steps = array();

        if (!$this->isCustomerLoggedIn()) {
            //$steps['login'] = $this->getCheckout()->getStepData('login');
        }

        $stepCodes = array('billing', 'shipping', 'shipping_method', 'payment', 'review');

        foreach ($stepCodes as $step) {
            $steps[$step] = $this->getCheckout()->getStepData($step);
        }
        return $steps;
    }

    public function getActiveStep()
    {
        //return $this->isCustomerLoggedIn() ? 'billing' : 'login';
        return $this->isCustomerLoggedIn() ? 'billing' : 'billing';
    }
}