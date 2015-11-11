<?php
$setup = new Mage_Catalog_Model_Resource_Setup('core_setup');

$attributeSetModel = Mage::getModel('eav/entity_attribute_set');
$attributeSetModel->setEntityTypeId($setup->getEntityTypeId('catalog_product'));
$attributeSetModel->setAttributeSetName('LED');
$attributeSetModel->save();
$attributeSetModel->initFromSkeleton($setup->getAttributeSetId('catalog_product', 'Default'));
$attributeSetModel->save();

$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();

$attributeSetId = $setup->getAttributeSetId('catalog_product', 'LED');

$setup->addAttributeGroup('catalog_product', 'LED', 'LED', 1000);

$installer->addAttribute(
    'catalog_product',
    'tim_led_potency',
    array(
        'user_defined' => true,
        'group' => 'LED',
        'used_in_product_listing' => true,
        'type' => 'decimal',
        'class' => 'validate-digits',
        'label' => 'Moc W',
        'required' => false,
        'sort_order' => 9
    )
);

$setup->addAttributeToSet('catalog_product', $attributeSetId, 'LED', 'tim_led_potency', 10);

$installer->addAttribute(
    'catalog_product',
    'tim_bulb_potency',
    array(
        'user_defined' => true,
        'group' => 'LED',
        'used_in_product_listing' => true,
        'type' => 'decimal',
        'class' => 'validate-digits',
        'label' => 'Odpowiednik standardowej mocy',
        'required' => false,
        'sort_order' => 9
    )
);

$setup->addAttributeToSet('catalog_product', $attributeSetId, 'LED', 'tim_bulb_potency', 10);

$installer->addAttribute(
    'catalog_product',
    'tim_handle',
    array(
        'user_defined' => true,
        'group' => 'LED',
        'used_in_product_listing' => true,
        'type' => 'varchar',
        'label' => 'Trzonek',
        'required' => false,
        'sort_order' => 9
    )
);

$setup->addAttributeToSet('catalog_product', $attributeSetId, 'LED', 'tim_handle', 10);

$installer->addAttribute(
    'catalog_product',
    'tim_color_light',
    array(
        'user_defined' => true,
        'group' => 'LED',
        'used_in_product_listing' => true,
        'type' => 'int',
        'input' => 'select',
        'label' => 'Barwa światła',
        'required' => false,
        'sort_order' => 9,
        'option' => array(
            'values' => array(
                0 => 'biały ciepły',
                1 => 'biały zimny',
            )
        ),
    )
);

$setup->addAttributeToSet('catalog_product', $attributeSetId, 'LED', 'tim_color_light', 10);

$installer->addAttribute(
    'catalog_product',
    'tim_calvin',
    array(
        'user_defined' => true,
        'group' => 'LED',
        'used_in_product_listing' => true,
        'type' => 'decimal',
        'class' => 'validate-digits',
        'label' => 'Barwa światła (K)',
        'required' => false,
        'sort_order' => 9
    )
);

$setup->addAttributeToSet('catalog_product', $attributeSetId, 'LED', 'tim_calvin', 10);

$installer->addAttribute(
    'catalog_product',
    'tim_voltage',
    array(
        'user_defined' => true,
        'group' => 'LED',
        'used_in_product_listing' => true,
        'type' => 'varchar',
        'label' => 'Napięcie',
        'required' => false,
        'sort_order' => 9
    )
);

$setup->addAttributeToSet('catalog_product', $attributeSetId, 'LED', 'tim_voltage', 10);

$installer->addAttribute(
    'catalog_product',
    'tim_led_hour',
    array(
        'user_defined' => true,
        'group' => 'LED',
        'used_in_product_listing' => true,
        'type' => 'decimal',
        'class' => 'validate-digits',
        'label' => 'Trwałość',
        'required' => false,
        'sort_order' => 9
    )
);

$setup->addAttributeToSet('catalog_product', $attributeSetId, 'LED', 'tim_led_hour', 10);

$installer->addAttribute(
    'catalog_product',
    'tim_lumen',
    array(
        'user_defined' => true,
        'group' => 'LED',
        'used_in_product_listing' => true,
        'type' => 'decimal',
        'class' => 'validate-digits',
        'label' => 'Moc strumienia świetlnego (Lm)',
        'required' => false,
        'sort_order' => 9
    )
);

$setup->addAttributeToSet('catalog_product', $attributeSetId, 'LED', 'tim_lumen', 10);

$installer->addAttribute(
    'catalog_product',
    'tim_angle',
    array(
        'user_defined' => true,
        'group' => 'LED',
        'used_in_product_listing' => true,
        'type' => 'decimal',
        'class' => 'validate-digits',
        'label' => 'Kąt rozsyłu',
        'required' => false,
        'sort_order' => 9
    )
);

$setup->addAttributeToSet('catalog_product', $attributeSetId, 'LED', 'tim_angle', 10);

$installer->addAttribute(
    'catalog_product',
    'tim_light_performance',
    array(
        'user_defined' => true,
        'group' => 'LED',
        'used_in_product_listing' => true,
        'type' => 'decimal',
        'class' => 'validate-digits',
        'label' => 'Wydajność świetlna (lm/W)',
        'required' => false,
        'sort_order' => 9
    )
);

$setup->addAttributeToSet('catalog_product', $attributeSetId, 'LED', 'tim_light_performance', 10);

$installer->addAttribute(
    'catalog_product',
    'tim_light_power',
    array(
        'user_defined' => true,
        'group' => 'LED',
        'used_in_product_listing' => true,
        'type' => 'decimal',
        'class' => 'validate-digits',
        'label' => 'Moc strumienia świetlnego (Cd)',
        'required' => false,
        'sort_order' => 9
    )
);

$setup->addAttributeToSet('catalog_product', $attributeSetId, 'LED', 'tim_light_power', 10);

$installer->addAttribute(
    'catalog_product',
    'tim_color_ra',
    array(
        'user_defined' => true,
        'group' => 'LED',
        'used_in_product_listing' => true,
        'type' => 'decimal',
        'class' => 'validate-digits',
        'label' => 'Współczynnik oddawania barw Ra',
        'required' => false,
        'sort_order' => 9
    )
);

$setup->addAttributeToSet('catalog_product', $attributeSetId, 'LED', 'tim_color_ra', 10);

$installer->addAttribute(
    'catalog_product',
    'tim_led_cycle',
    array(
        'user_defined' => true,
        'group' => 'LED',
        'used_in_product_listing' => true,
        'type' => 'decimal',
        'class' => 'validate-digits',
        'label' => 'Ilość cykli pracy',
        'required' => false,
        'sort_order' => 9
    )
);

$setup->addAttributeToSet('catalog_product', $attributeSetId, 'LED', 'tim_led_cycle', 10);

$installer->addAttribute(
    'catalog_product',
    'tim_led_ignition',
    array(
        'user_defined' => true,
        'group' => 'LED',
        'used_in_product_listing' => true,
        'type' => 'decimal',
        'class' => 'validate-number',
        'label' => 'Zapłon (sec)',
        'required' => false,
        'sort_order' => 9
    )
);

$setup->addAttributeToSet('catalog_product', $attributeSetId, 'LED', 'tim_led_ignition', 10);

$installer->addAttribute(
    'catalog_product',
    'tim_class_energy',
    array(
        'user_defined' => true,
        'group' => 'LED',
        'used_in_product_listing' => true,
        'type' => 'varchar',
        'label' => 'Efektywność energetyczna',
        'required' => false,
        'sort_order' => 9
    )
);

$setup->addAttributeToSet('catalog_product', $attributeSetId, 'LED', 'tim_class_energy', 10);

$installer->addAttribute(
    'catalog_product',
    'tim_led_size',
    array(
        'user_defined' => true,
        'group' => 'LED',
        'used_in_product_listing' => true,
        'type' => 'int',
        'input' => 'select',
        'label' => 'Wymiary lampy (szer x wys, mm)',
        'required' => false,
        'sort_order' => 9,
        'option' => array(
            'values' => array(
                0 => '35 x 106',
                1 => '60 x 110',
                2 => '50 x 55',
            )
        ),
    )
);

$setup->addAttributeToSet('catalog_product', $attributeSetId, 'LED', 'tim_led_size', 10);

$installer->endSetup();