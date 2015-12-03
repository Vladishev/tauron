<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Tauron
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */

$installer = $this;

$installer->startSetup();
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$staticBlocks = array(
    array(
        'title' => 'Popup',
        'identifier' => 'tauron-popup',
//        'content' => '',
        'is_active' => 1,
        'stores' => array(0),
    ), array(
        'title' => 'tim_tabs1',
        'identifier' => 'tim_tabs1',
        'content' => 'tim_tabs1',
        'is_active' => 1,
        'stores' => array(0),
    ), array(
        'title' => 'tim_tabs2',
        'identifier' => 'tim_tabs2',
        'content' => 'tim_tabs2',
        'is_active' => 1,
        'stores' => array(0),
    ), array(
        'title' => 'tim_tabs3',
        'identifier' => 'tim_tabs3',
        'content' => 'tim_tabs3',
        'is_active' => 1,
        'stores' => array(0),
    ), array(
        'title' => 'tim_tabs4',
        'identifier' => 'tim_tabs4',
        'content' => 'tim_tabs4',
        'is_active' => 1,
        'stores' => array(0),
    ),
);

foreach ($staticBlocks as $data) {
    Mage::getModel('cms/block')->setData($data)->save();
}

$installer->endSetup();