<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Tauron
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Tauron_Block_Adminhtml_ComplaintReport extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Init grid container
     */
    public function __construct()
    {
        $this->_blockGroup = 'tim_tauron';
        $this->_controller = 'adminhtml_complaintReport';
        $this->_headerText = Mage::helper('tim_tauron')->__('Complaints');

        parent::__construct();
        $this->_removeButton('add');
    }
}