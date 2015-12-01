<?php
$attrGroupName = 'Crm Group';
$attrNote = 'Crm Note';
$attribute = array('tim_crm_id_customer','tim_guid_customer','tim_mfg_id','tim_account_number',
                   'tim_main_contact_crm','tim_main_contact_tim','tim_crm_id_address','tim_guid_address');

$installer = $this;
$installer->startSetup();
$setup = Mage::getModel('customer/entity_setup', 'core_setup');

    $setup->addAttribute('customer', 'tim_crm_id_customer', array(
        'group' => $attrGroupName,
        'sort_order' => 7,
        'type' => 'text',
        'backend' => '',
        'frontend' => '',
        'label' => 'CRM ID',
        'note' => 'CRM ID',
        'input' => 'text',
        'class' => '',
        'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => true,
        'required' => false,
        'user_defined' => true,
        'default' => '',
        'visible_on_front' => true,
        'unique' => false,
        'is_configurable' => false,
        'used_for_promo_rules' => true
    ));
    $setup->addAttribute('customer', 'tim_guid_customer', array(
        'group' => $attrGroupName,
        'sort_order' => 7,
        'type' => 'text',
        'backend' => '',
        'frontend' => '',
        'label' => 'GUID',
        'note' => 'GUID',
        'input' => 'text',
        'class' => '',
        'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => true,
        'required' => false,
        'user_defined' => true,
        'default' => '',
        'visible_on_front' => true,
        'unique' => false,
        'is_configurable' => false,
        'used_for_promo_rules' => true
    ));
    $setup->addAttribute('customer_address', 'tim_crm_id_address', array(
        'group' => $attrGroupName,
        'sort_order' => 7,
        'type' => 'text',
        'backend' => '',
        'frontend' => '',
        'label' => 'CRM ID',
        'note' => 'CRM ID',
        'input' => 'text',
        'class' => '',
        'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => true,
        'required' => false,
        'user_defined' => true,
        'default' => '',
        'visible_on_front' => true,
        'unique' => false,
        'is_configurable' => false,
        'used_for_promo_rules' => true
    ));
    $setup->addAttribute('customer_address', 'tim_guid_address', array(
        'group' => $attrGroupName,
        'sort_order' => 7,
        'type' => 'text',
        'backend' => '',
        'frontend' => '',
        'label' => 'GUID',
        'note' => 'GUID',
        'input' => 'text',
        'class' => '',
        'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => true,
        'required' => false,
        'user_defined' => true,
        'default' => '',
        'visible_on_front' => true,
        'unique' => false,
        'is_configurable' => false,
        'used_for_promo_rules' => true
    ));
    $setup->addAttribute('customer', 'tim_mfg_id', array(
        'group' => $attrGroupName,
        'sort_order' => 7,
        'type' => 'text',
        'backend' => '',
        'frontend' => '',
        'label' => 'MFG ID',
        'note' => 'MFG ID',
        'input' => 'text',
        'class' => '',
        'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => true,
        'required' => false,
        'user_defined' => true,
        'default' => '',
        'visible_on_front' => true,
        'unique' => false,
        'is_configurable' => false,
        'used_for_promo_rules' => true
    ));
    $setup->addAttribute('customer', 'tim_account_number', array(
        'group' => $attrGroupName,
        'sort_order' => 7,
        'type' => 'text',
        'backend' => '',
        'frontend' => '',
        'label' => 'Nr konta bankowego (IRB)',
        'note' => 'Nr konta bankowego (IRB)',
        'input' => 'text',
        'class' => '',
        'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => true,
        'required' => false,
        'user_defined' => true,
        'default' => '',
        'visible_on_front' => true,
        'unique' => false,
        'is_configurable' => false,
        'used_for_promo_rules' => true
    ));
    $setup->addAttribute('customer', 'tim_main_contact_crm', array(
        'group' => $attrGroupName,
        'sort_order' => 7,
        'type' => 'text',
        'backend' => '',
        'frontend' => '',
        'label' => 'ID głównego kontaktu w CRM',
        'note' => 'ID głównego kontaktu w CRM',
        'input' => 'text',
        'class' => '',
        'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => true,
        'required' => false,
        'user_defined' => true,
        'default' => '',
        'visible_on_front' => true,
        'unique' => false,
        'is_configurable' => false,
        'used_for_promo_rules' => true
    ));
    $setup->addAttribute('customer', 'tim_main_contact_tim', array(
        'group' => $attrGroupName,
        'sort_order' => 7,
        'type' => 'text',
        'backend' => '',
        'frontend' => '',
        'label' => 'Dane osoby kontaktowej w TIM',
        'note' => 'Dane osoby kontaktowej w TIM',
        'input' => 'text',
        'class' => '',
        'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => true,
        'required' => false,
        'user_defined' => true,
        'default' => '',
        'visible_on_front' => true,
        'unique' => false,
        'is_configurable' => false,
        'used_for_promo_rules' => true
    ));
    
    foreach($attribute as $attr){
          
        if(strpos($attr, 'address')) {    
            Mage::getSingleton('eav/config')
            ->getAttribute('customer_address', $attr)
            ->setData('used_in_forms',array ('adminhtml_customer_address'))
            ->save();  
        }
        else 
        {  
            Mage::getSingleton('eav/config')
            ->getAttribute('customer', $attr)
            ->setData('used_in_forms',array('adminhtml_customer'))
            ->save();     
        }
    }    
$installer->endSetup();
