<?php


class Tim_Crm_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{
    protected function _preparePage()
    {
        $this->getCollection()
            ->getSelect()
            ->join(array('sfo' => 'sales_flat_order'), '`main_table`.entity_id=sfo.entity_id',
                array(
                    'tim_chance_id' => 'sfo.tim_chance_id',
                    'tim_sent_to_crm' => 'sfo.tim_sent_to_crm',
                    'tim_info' => 'sfo.tim_info',
                    'tim_error' => 'sfo.tim_error',
                ), null, 'left'
            );

        return parent::_preparePage();
    }

    protected function _prepareColumns()
    {

        $this->addColumnAfter('tim_chance_id', array(
            'header' => Mage::helper('sales')->__('CRM #'),
            'width' => '80px',
            'type' => 'text',
            'renderer' => 'Tim_Crm_Block_Adminhtml_Sales_Order_Renderer_ChanceId',
            'filter_condition_callback' => array($this, '_chanceIdFilter'),
            'index' => 'tim_chance_id'
        ), 'real_order_id');

        parent::_prepareColumns();

        foreach ($this->filterIndexColumnNames() as $columnName) {
            $this->getColumn($columnName)->setFilterIndex('main_table.' . $this->getColumn($columnName)->getIndex());
        }
    }

    protected function _chanceIdFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $value = str_replace(array('ą', 'ę', 'ó', 'ż', 'ź', 'ł', 'ń', 'ć', 'ś'), array('a', 'e', 'o', 'z', 'z', 'l', 'n', 'c', 's'), trim(strtolower($value)));
        if (ctype_alpha($value)) {
            if (strstr('blad', $value) !== false) {
                $collection->getSelect()->where("sfo.tim_error = ?", 1);
            }
            if (strstr('przygotowany do wysylki', $value) !== false) {
                $collection->getSelect()
                    ->where("sfo.tim_chance_id is null")
                    ->where("(sfo.tim_error is null or sfo.tim_error = 0)
                                and (sfo.tim_sent_to_crm is null or sfo.tim_sent_to_crm = 0)");
            }
        } else {
            $collection->addFieldToFilter('tim_chance_id', array('like' => '%' . $value . '%'));
        }

        return $this;
    }

    protected function filterIndexColumnNames()
    {
        $filterIndexColumnNames = array(
            'created_at',
            'real_order_id',
            'base_grand_total',
            'grand_total',
            'status'
        );
        if (!Mage::app()->isSingleStoreMode()) {
            $filterIndexColumnNames[] .= 'store_id';
        }

        return $filterIndexColumnNames;
    }

}
