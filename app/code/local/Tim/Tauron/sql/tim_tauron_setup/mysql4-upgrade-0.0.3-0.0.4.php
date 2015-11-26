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

$installer->endSetup();