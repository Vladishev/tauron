<?php

$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$name = 'tim_crm_id';
$setup->removeAttribute('catalog_product', $name);

$setup->addAttribute('catalog_product', $name, array(
    'label' => 'CRMID produktu',
    'group' => 'Inventory',
    'type' => 'varchar',
    'input' => 'text',
    'backend' => '',
    'frontend' => '',
    'source' => '', //_producttype',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'is_searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
    'unique' => false
));

$installer->endSetup();