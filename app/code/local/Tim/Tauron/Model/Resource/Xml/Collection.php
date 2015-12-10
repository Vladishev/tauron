<?php

/**
 * Tim
 *
 * @category   Tim
 * @package   Tim_Tauron
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Tauron_Model_Resource_Xml_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize a collection, set model for it
     */
    protected function _construct()
    {
        $this->_init('tim_tauron/xml');
    }
}