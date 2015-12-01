<?php

$installer = $this;
$installer->startSetup();

$attribute = array();
$attribute[] = array('code' => 'tim_jednostka_miary', 'label' => 'Jednostka miary', 'type' => 'text', 'backend' => '');

$objCatalogEavSetup = Mage::getResourceModel('catalog/eav_mysql4_setup', 'core_setup');

foreach ($attribute as $key => $attr) {
    $attrIdTest = $objCatalogEavSetup->getAttributeId(Mage_Catalog_Model_Product::ENTITY, $attr['code']);

    if ($attrIdTest === false) {
        $arr = array(
            'group' => 'import_pim',
            'type' => $attr['type'],
            'backend' => $attr['backend'],
            'frontend' => '',
            'label' => $attr['label'],
            'input' => $attr['type'],
            'class' => '',
            'source' => '',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
            'frontend_input' => '',
            'frontend_class' => '',
            'is_comparable' => '0',
            'visible' => '1',
            'is_required' => '0',
            'user_defined' => '1',
            'default' => '',
            'is_visible_on_front' => '0',
            'is_unique' => '0',
            'is_configurable' => '1',
            'is_filterable' => '1',
            'is_filterable_in_search' => '1',
            'is_searchable' => '0',
            'is_visible_in_advanced_search' => '0',
            'is_used_for_promo_rules' => '0',
            'position' => '0',
            'is_html_allowed_on_front' => '1',
            'used_in_product_listing' => '0'
        );
        if (!empty($attr['options'])) {
            $arr['option'] = $attr['options'];
        }
        if (!empty($attr['input'])) {
            $arr['input'] = $attr['input'];
        }
        if (!empty($attr['source'])) {
            $arr['source'] = $attr['source'];
        }
        $objCatalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, $attr['code'], $arr);
    }
}

$installer->endSetup();