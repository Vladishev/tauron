<?php

class Uluruz_Przelewy24_Block_Info_Przelewy24 extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('uluruz/przelewy24/info.phtml');
    }
}
