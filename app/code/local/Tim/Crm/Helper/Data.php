<?php

class Tim_Crm_Helper_Data extends Mage_Core_Helper_Data 
{
    public function getTaxRate($_item)
    {
        $store = $_item->getProduct()->getStoreId();
        $request = Mage::getSingleton('tax/calculation')->getRateRequest(null, null, null, $store);
        $taxclassid = $_item->getProduct()->getData('tax_class_id');
        $percent = Mage::getSingleton('tax/calculation')->getRate($request->setProductClassId($taxclassid));
        
        return $percent * 0.01;
    }
    
    public function getProductBarrelGroup($params)
    {
        $barrels = array();
        if(isset($params['barrels'])){
            foreach($params['barrels'] as $bar){
                $barrels[$bar['barrel']['name']] = array(
                    'name' => $bar['barrel']['name'],
                    'crm_id' => $bar['barrel']['crm_id'],
                    'qty' => isset($barrels[$bar['barrel']['name']]) ? $barrels[$bar['barrel']['name']]['qty'] + 1 : 1,
                    'deposit' => $bar['barrel']['deposit']
                );
            }
        }
        
        return $barrels;
    }
}