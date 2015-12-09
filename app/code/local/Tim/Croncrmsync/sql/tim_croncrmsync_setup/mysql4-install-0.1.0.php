<?php

$installer = $this;
$installer->startSetup();

    $installer->run("
        ALTER TABLE {$this->getTable('sales/quote')}
        ADD `Tracking_link` varchar (128) null;");
    $installer->run("
        ALTER TABLE {$this->getTable('sales_flat_order')}
        ADD `Tracking_link` varchar (128) null;"); 
        
$installer->endSetup();		