<?php

$installer = $this;

$installer->startSetup();

$installer->getConnection()
    ->addColumn(
        $installer->getTable('sales_flat_order'),
        'tim_chance_id',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => null,
            'unsigned' => true,
            'nullable' => true,
            'comment' => 'overal information'
        )
    );

$installer->endSetup();