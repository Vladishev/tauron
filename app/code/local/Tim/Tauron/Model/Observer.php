<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Tauron
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Tauron_Model_Observer
{
    public function closeAccess()
    {
        if (Mage::helper('tim_tauron')->checkGuest()) {
            if (Mage::helper('core/http')->getHttpReferer()) {
                $url = Mage::helper('core/http')->getHttpReferer() . '?popup=1';
            } else {
                $url = Mage::getUrl() . '?popup=1';
            }
            Mage::app()->getFrontController()->getResponse()->setRedirect($url);
            Mage::app()->getResponse()->sendResponse();
            exit;
        }
    }

    /**
     * Redirect to product, when category have only one product
     */
    public function categoryRedirect(Varien_Event_Observer $observer)
    {
        $categoryId = $observer->getEvent()->getCategory()->getEntityId();
        $products = Mage::getModel('catalog/category')->load($categoryId)
            ->getProductCollection()
            ->addAttributeToFilter('status', 1)// enabled
            ->addAttributeToFilter('visibility', 4); //visibility in catalog,search
        if (sizeof($products->getData()) == 1) {
            $productInfo = array_shift($products->getData());
            $productId = $productInfo['entity_id'];
            $productUrl = Mage::getModel('catalog/product')->load($productId)->getProductUrl();
            Mage::app()->getResponse()->setRedirect($productUrl);
        }
    }

    /**
     * Saves data from customer url to sales_flat_order table
     * to new order
     * @param Varien_Event_Observer $observer
     */
    public function saveUrlData(Varien_Event_Observer $observer)
    {
        $orderId = $observer->getEvent()->getOrder()->getId();
        $tauronCart = Mage::getSingleton('checkout/session')->getData('tauron_cart');
        $order = Mage::getModel('sales/order')
            ->load($orderId, 'entity_id')
            ->setTimTauronCustomer($tauronCart['pesel'])
            ->setTimTauronOrder($tauronCart['businessId'])
            ->setTimTauronEmployee($tauronCart['employee']);
        try {
            $order->save();
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'tim_tauron.log');
        }
    }

    /**
     * Saves 'b2c' or 'b2c' attribute to 'customer_type'
     * @param Varien_Event_Observer $observer
     */
    public function saveCustomerAttributes(Varien_Event_Observer $observer)
    {
        $customerType = Mage::helper('tim_tauron')->getCustomerType();
        if ($customerType) {
            $customerId = $observer->getEvent()->getOrder()->getCustomerId();
            $customer = Mage::getModel('customer/customer')->load($customerId);
            $customer->setCustomerType($customerType);
            try {
                $customer->save();
            } catch (Exception $e) {
                Mage::log($e->getMessage(), null, 'tim_tauron.log');
            }
        }
    }
}