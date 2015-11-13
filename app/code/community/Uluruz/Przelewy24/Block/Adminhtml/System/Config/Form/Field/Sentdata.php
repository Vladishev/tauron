<?php

class Uluruz_Przelewy24_Block_Adminhtml_System_Config_Form_Field_Sentdata extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('uluruz/przelewy24/system/config/generatebtn.phtml');
        }
        
        return $this;
    }
    
    
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $originalData = $element->getOriginalData();

        $website = $this->getRequest()->getParam('website');
        $store   = $this->getRequest()->getParam('store');
        
        $uri = $originalData['button_url'];
        if ($store != "")
        	$uri .= '/website/'.$website;
        if ($store != "")
        	$uri .= '/store/'.$store;
        
        $uri = Mage::helper('adminhtml')->getUrl($uri);
        $this->addData(array(
            'button_label'	=> Mage::helper('przelewy24')->__($originalData['button_label']),
            'button_url'	=> $uri,
            'html_id'		=> $element->getHtmlId(),
            'value'             => str_replace('"', "'", $element->getValue())
        ));
        return $this->_toHtml();
    }

	public function getButtonLabel()
	{
		return Mage::helper('przelewy24')->__( 'Wyślij powiadomienie do Przelewy24' );
	}
        
	public function getButtonUrl()
	{
		$website = $this->getRequest()->getParam('website');
		$store = $this->getRequest()->getParam('store');
//		$uri = 'importff_admin/adminhtml_importff/generate';
		if ($store != "") $uri .= '/website/'.$website;
		if ($store != "") $uri .= '/store/'.$store;
		$uri = Mage::helper('adminhtml')->getUrl($uri);
		return $uri;
	}
	
	
}
