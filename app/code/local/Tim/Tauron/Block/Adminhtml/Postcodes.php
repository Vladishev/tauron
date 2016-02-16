<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Tauron
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Tauron_Block_Adminhtml_Postcodes extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Init grid container
     */
    public function __construct()
    {
        $this->_blockGroup = 'tim_tauron';
        $this->_controller = 'adminhtml_postcodes';
        $this->_headerText = Mage::helper('tim_tauron')->__('ZIP codes');

        parent::__construct();
        $this->_removeButton('add');
    }
}