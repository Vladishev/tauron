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
$connection = $installer->getConnection();

$tauronComplaintTable = $installer->getTable('tim_tauron/complaint');

$installer->startSetup();

if (!$connection->isTableExists($tauronComplaintTable)) {
    $table = $connection->newTable($tauronComplaintTable)
        ->addColumn('complaint_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'nullable' => false,
            'primary' => true,
        ), 'Complaint Id')
        ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => true,
        ), 'Order Id')
        ->addForeignKey($installer->getFkName('tim_tauron/complaint', 'order_id', 'sales/order', 'entity_id'),
            'order_id', $installer->getTable('sales/order'), 'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addColumn('first_name', Varien_Db_Ddl_Table::TYPE_TEXT, '255', array(
            'nullable' => true,
        ), 'First name')
        ->addColumn('last_name', Varien_Db_Ddl_Table::TYPE_TEXT, '255', array(
            'nullable' => true,
        ), 'Last name')
        ->addColumn('street', Varien_Db_Ddl_Table::TYPE_TEXT, '255', array(
            'nullable' => true,
        ), 'Street')
        ->addColumn('city', Varien_Db_Ddl_Table::TYPE_TEXT, '255', array(
            'nullable' => true,
        ), 'City')
        ->addColumn('zip', Varien_Db_Ddl_Table::TYPE_TEXT, '255', array(
            'nullable' => true,
        ), 'Zip')
        ->addColumn('phone', Varien_Db_Ddl_Table::TYPE_TEXT, '255', array(
            'nullable' => true,
        ), 'Phone')
        ->addColumn('email', Varien_Db_Ddl_Table::TYPE_TEXT, '255', array(
            'nullable' => true,
        ), 'Email')
        ->addColumn('comment', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
            'nullable' => true,
        ), 'Comment');
    $connection->createTable($table);
}

$installer->endSetup();