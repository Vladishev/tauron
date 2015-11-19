<?php

class Tim_Crm_Block_Adminhtml_Customer_Edit_Tab_Orders extends Mage_Adminhtml_Block_Customer_Edit_Tab_Orders
{
    
    protected function _preparePage()
    {

        $this->getCollection()
            ->getSelect()
            ->join('sales_flat_order', 'main_table.entity_id=sales_flat_order.entity_id', 
                    array(
                        'tim_chance_id'=>'tim_chance_id',
                        'tim_sent_to_crm'=>'tim_sent_to_crm',
                        'tim_info'=>'tim_info',
                        'tim_error'=>'tim_error'), null,'left');
        return parent::_preparePage();
    }
    
    protected function _prepareColumns()
    {
        
        $this->addColumnAfter('tim_chance_id', array(
            'header'=> Mage::helper('sales')->__('CRM #'),
            'width' => '80px',
            'type'  => 'text',
            'renderer' => 'Tim_Crm_Block_Adminhtml_Sales_Order_Renderer_ChanceId',
            'index' => 'tim_chance_id'
        ),'increment_id');

        return parent::_prepareColumns();
    }

}
