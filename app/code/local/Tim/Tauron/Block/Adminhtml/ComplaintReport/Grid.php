<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Tauron
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Tauron_Block_Adminhtml_ComplaintReport_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Init grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('tim_tauron_complaint_grid');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('tim_tauron/complaint')->getCollection();
        $collection->getSelect()->columns(new Zend_Db_Expr("CONCAT(first_name,' ',last_name) AS name"));

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
        $this->addColumn('complaint_id', array(
            'header' => Mage::helper('tim_tauron')->__('ID'),
            'width' => '10',
            'index' => 'complaint_id',
            'filter_index' => 'complaint_id'
        ));
        $this->addColumn('order_id', array(
            'header' => Mage::helper('tim_tauron')->__('Order Id'),
            'width' => '10',
            'index' => 'order_id',
            'filter_index' => 'order_id'
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('tim_tauron')->__('Name'),
            'width' => '70',
            'index' => 'name',
            'filter' => false,
        ));
        $this->addColumn('street', array(
            'header' => Mage::helper('tim_tauron')->__('Street'),
            'width' => '10',
            'index' => 'street',
            'filter_index' => 'street'
        ));
        $this->addColumn('city', array(
            'header' => Mage::helper('tim_tauron')->__('City'),
            'width' => '10',
            'index' => 'city',
            'filter_index' => 'city'
        ));
        $this->addColumn('zip', array(
            'header' => Mage::helper('tim_tauron')->__('Zip'),
            'width' => '10',
            'index' => 'zip',
            'filter_index' => 'zip'
        ));
        $this->addColumn('phone', array(
            'header' => Mage::helper('tim_tauron')->__('Phone'),
            'width' => '10',
            'index' => 'phone',
            'filter_index' => 'phone'
        ));
        $this->addColumn('email', array(
            'header' => Mage::helper('tim_tauron')->__('Email'),
            'width' => '10',
            'index' => 'email',
            'filter_index' => 'email'
        ));
        $this->addColumn('comment', array(
            'header' => Mage::helper('tim_tauron')->__('Comment'),
            'width' => '10',
            'index' => 'comment',
            'filter_index' => 'comment'
        ));

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