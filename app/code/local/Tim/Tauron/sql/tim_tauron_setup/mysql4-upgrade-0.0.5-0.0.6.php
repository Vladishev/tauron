<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Tauron
 * @copyright  Copyright (c) 2016 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */

$installer = $this;
$installer->startSetup();

$objCatalogEavSetup = Mage::getResourceModel('catalog/eav_mysql4_setup', 'core_setup');

$attrIdTest = $objCatalogEavSetup->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'tim_ean');

if ($attrIdTest === false) {
    $arr = array(
        'group' => 'General',
        'type' => 'varchar',
        'backend' => '',
        'frontend' => '',
        'label' => 'Tim EAN',
        'input' => 'text',
        'class' => '',
        'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'frontend_input' => '',
        'frontend_class' => '',
        'is_comparable' => 0,
        'visible' => '1',
        'required' => false,
        'user_defined' => '1',
        'default' => '',
        'is_visible_on_front' => 0,
        'is_unique' => 1,
        'is_configurable' => '1',
        'is_filterable' => '1',
        'is_filterable_in_search' => '1',
        'is_searchable' => '0',
        'is_visible_in_advanced_search' => 0,
        'is_used_for_promo_rules' => 0,
        'position' => '0',
        'is_html_allowed_on_front' => '1',
        'used_in_product_listing' => 0
    );

    $objCatalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'tim_ean', $arr);
}

$installer->endSetup();