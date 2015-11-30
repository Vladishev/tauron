<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Tauron
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */

$installer = $this;

$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('tim_tauron/complaint'),
        'broken_prod_id',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => true,
            'comment' => 'Broken products id'
        )
    );
$staticBlock = array(
        'title' => 'Complaint form',
        'identifier' => 'tim_complaint',
        'content' => '{{block type="core/template" name="tim_complaint" template="tim/complaint_form.phtml"}}',
        'is_active' => 1,
        'stores' => array(0),
    );
Mage::getModel('cms/block')->setData($staticBlock)->save();

$installer->endSetup();