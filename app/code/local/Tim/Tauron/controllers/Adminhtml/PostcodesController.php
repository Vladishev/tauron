<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Tauron
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Tauron_Adminhtml_PostcodesController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Opinion'));
        $this->loadLayout();
        $this->_setActiveMenu('report/tim');
        $this->_addContent($this->getLayout()->createBlock('tim_tauron/adminhtml_postcodes'));
        $this->renderLayout();
    }

    /**
     * Grid action
     *
     * @return null
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('tim_tauron/adminhtml_postcodes_grid')->toHtml()
        );
    }

    /**
     * Export recommendation grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName = 'postcodes-'.time().'.csv';
        $grid = $this->getLayout()->createBlock('tim_tauron/adminhtml_postcodes_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/tim/tim_zip_codes_report');
    }
}