<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Tauron
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Tauron_Block_Adminhtml_Postcodes_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Init grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('tim_tauron_zip_codes_grid');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('sales/order')->getCollection();
        $collection->getSelect()->joinLeft(array('sfoa' => 'sales_flat_order_address'), 'main_table.entity_id = sfoa.parent_id AND address_type = "billing"', array('postcode'));

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return Tim_Tauron_Block_Adminhtml_ComplaintReport_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('postcode', array(
            'header' => Mage::helper('tim_tauron')->__('ZIP codes'),
            'width' => '10',
            'index' => 'postcode',
            'filter_index' => 'sfoa.postcode'
        ));
        $this->addColumn('created_at', array(
            'header' => Mage::helper('tim_tauron')->__('Order date'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '200',
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));

        return parent::_prepareColumns();
    }

    /**
     * Returns a grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}