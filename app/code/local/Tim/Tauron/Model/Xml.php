<?php

/**
 * Tim
 *
 * @category   Tim
 * @package   Tim_Tauron
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Tauron_Model_Xml extends Mage_Core_Model_Abstract
{
    /**
     * Initialize complaint model, set resource model for it
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('tim_tauron/xml');
    }
}