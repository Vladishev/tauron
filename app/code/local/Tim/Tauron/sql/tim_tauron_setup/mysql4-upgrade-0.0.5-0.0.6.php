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

$staticBlock = array(
    'title' => 'Discount',
    'identifier' => 'tim_discount',
    'content' => '{{block type="tim_tauron/discount" name="tim_discount" template="tim/discount.phtml"}}',
    'is_active' => 1,
    'stores' => array(0),
);

Mage::getModel('cms/block')->setData($staticBlock)->save();

if (Mage::getModel('admin/block')) {
    $installer->getConnection()->insertMultiple(
        $installer->getTable('admin/permission_block'),
        array(
            array('block_name' => 'tim_tauron/discount', 'is_allowed' => 1),
        )
    );
}

$installer->endSetup();