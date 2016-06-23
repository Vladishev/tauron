<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Tauron
 * @copyright  Copyright (c) 2016 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */

$installer = $this;

$installer->startSetup();
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$data =  array(
    'title' => 'LED Popup',
    'identifier' => 'led_tauron-popup',
    'is_active' => 1,
    'stores' => array(0),
);

Mage::getModel('cms/block')->setData($data)->save();

$installer->endSetup();