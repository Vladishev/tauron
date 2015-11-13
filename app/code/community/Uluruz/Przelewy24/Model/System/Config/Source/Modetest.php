<?php

class Uluruz_Przelewy24_Model_System_Config_Source_Modetest
{
    public function toOptionArray(){
        return array(
            array('value' => '0', 'label'=>'Test transakcji poprawnej'),
            array('value' => '1', 'label'=>'Test transakcji błędnej'),
        );
    }	

}