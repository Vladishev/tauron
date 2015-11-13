<?php

class Uluruz_Przelewy24_Model_System_Config_Source_Mode
{
    public function toOptionArray(){
        return array(
            array('value' => '0', 'label'=>'Testowy anonimowy'),
            array('value' => '1', 'label'=>'Testowy'),
            array('value' => '2', 'label'=>'Produkcyjny'),
        );
    }	

}