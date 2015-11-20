<?php

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$setup->addAttribute('customer', 'customer_type', array(
    'label' => 'Typ klienta',
    'type' => 'varchar',
    'input' => 'select',
    'default_value' => 'b2c',
    'source' => 'account/source_option_customertype',
    'visible' => true,
    'required' => true,
    'position' => 1,
));

$installer->run("
update eav_attribute set note = 'b2b or b2c', frontend_input = 'text', source_model = '' where attribute_code = 'customer_type'
");

$installer->endSetup();