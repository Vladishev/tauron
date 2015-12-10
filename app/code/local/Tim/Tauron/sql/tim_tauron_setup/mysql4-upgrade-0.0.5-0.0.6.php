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

$tauronXmlTable = $installer->getTable('tim_tauron/xml');

$installer->startSetup();
if (!$connection->isTableExists($tauronXmlTable)) {
    $table = $connection->newTable($tauronXmlTable)
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'nullable' => false,
            'primary' => true,
        ), 'id')
        ->addColumn('business_id', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
        ), 'Business ID')
        ->addColumn('telephone', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
        ), 'Telephone')
        ->addColumn('email', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
        ), 'Email')
        ->addColumn('pesel', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
        ), 'Pesel')
        ->addColumn('city', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
        ), 'City')
        ->addColumn('zip_code', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
        ), 'Zip code')
        ->addColumn('street', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
        ), 'Street')
        ->addColumn('home', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
        ), 'Home')
        ->addColumn('flat', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
        ), 'Flat')
        ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
        ), 'Name')
        ->addColumn('surname', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
        ), 'Surname')
        ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
        ), 'SKU')
        ->addColumn('employee', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
        ), 'Employee')
        ->addColumn('url', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
        ), 'Url');
    $connection->createTable($table);
}

$installer->endSetup();