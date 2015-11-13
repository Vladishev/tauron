<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Tauron
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Tauron_Block_Checkout_Cart_Item_Renderer extends Mage_Bundle_Block_Checkout_Cart_Item_Renderer
{
    public function getOptionList()
    {
        $options = Mage::helper('bundle/catalog_product_configuration')->getOptions($this->getItem());
        foreach($options as $key => $value){
            $options[$key]['value'] = strstr($value['value'][0], '<span', true);
        }
        return $options;
    }
}
