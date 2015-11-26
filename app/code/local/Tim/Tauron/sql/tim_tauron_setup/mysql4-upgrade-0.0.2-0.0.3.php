<?php
/**
* Tim
*
 * @category   Tim
* @package    Tim_Recommendation
* @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vlad Verbitskiy <vladmsu@ukr.net>
 */

$installer = $this;

$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('sales/flat_order'),
        'tim_tauron_customer',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => false,
            'comment' => 'tim_tauron_customer'
        )
    );
$installer->getConnection()
    ->addColumn($installer->getTable('sales/flat_order'),
        'tim_tauron_order',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => false,
            'comment' => 'tim_tauron_order'
        )
    );
$installer->getConnection()
    ->addColumn($installer->getTable('sales/flat_order'),
        'tim_tauron_employee',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => false,
            'comment' => 'tim_tauron_employee'
        )
    );

$installer->endSetup();