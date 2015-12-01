<?php
$installer = $this;
$installer->startSetup();
$installer->getConnection()
    ->addColumn(
        $installer->getTable('sales/quote'),
        'tim_sent_to_crm',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
            'length' => null,
            'unsigned' => true,
            'nullable' => true,
            'comment' => 'sending progress'
        )
    );
$installer->getConnection()
    ->addColumn(
        $installer->getTable('sales/quote'),
        'tim_error',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
            'length' => null,
            'unsigned' => true,
            'nullable' => true,
            'comment' => 'error handling'
        )
    );
$installer->getConnection()
    ->addColumn(
        $installer->getTable('sales/quote'),
        'tim_info',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => null,
            'unsigned' => true,
            'nullable' => true,
            'comment' => 'overal information'
        )
    );

/*installation for the table sales_flat_orders*/
$installer->getConnection()
    ->addColumn(
        $installer->getTable('sales_flat_order'),
        'tim_sent_to_crm',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
            'length' => null,
            'unsigned' => true,
            'nullable' => true,
            'comment' => 'sending progress'
        )
    );
$installer->getConnection()
    ->addColumn(
        $installer->getTable('sales_flat_order'),
        'tim_error',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
            'length' => null,
            'unsigned' => true,
            'nullable' => true,
            'comment' => 'error handling'
        )
    );
$installer->getConnection()
    ->addColumn(
        $installer->getTable('sales_flat_order'),
        'tim_info',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => null,
            'unsigned' => true,
            'nullable' => true,
            'comment' => 'overal information'
        )
    );

$installer->endSetup();