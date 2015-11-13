<?php

class Uluruz_Przelewy24_Block_Form_Przelewy24 extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('uluruz/przelewy24/form.phtml');
    }
	
}
