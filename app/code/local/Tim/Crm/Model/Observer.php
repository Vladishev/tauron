<?php

class Tim_Crm_Model_Observer 
{
    public function updateOrder($observer)
    {
        $order = $observer->getEvent()->getOrder();
       
        $order->setTimSentToCrm(false);
        $order->setTimError(false);
        $order->save();
       
    }
}


