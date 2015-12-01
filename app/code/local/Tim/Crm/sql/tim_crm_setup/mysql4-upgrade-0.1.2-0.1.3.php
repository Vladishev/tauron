<?php

$installer = $this;
$installer->startSetup();

$installer->run("
    insert into customer_form_attribute(form_code, attribute_id) (SELECT 'adminhtml_customer', attribute_id FROM eav_attribute where attribute_code = 'customer_type');
");

$installer->endSetup();