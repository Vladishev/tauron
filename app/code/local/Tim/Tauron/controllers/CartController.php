<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Tauron
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Tauron_CartController extends Mage_Core_Controller_Front_Action
{
    /**
     * Attribute set name
     */
    const LED_ATTR_SET = 'LED';

    /**
     * Quantity of product
     */
    const QTY = 1;

    /**
     * Parameters for popup 1
     */
    const POPUP_1 = '/popup/1';

    /**
     * Parameters for popup 2
     */
    const POPUP_2 = '/popup/2';

    /**
     * Uncomment and put your data for test.
     * After this put http://tauron.local/tim_tauron/cart/ to url field.
     */
//    public function indexAction()
//    {
//        $businessId = '0012';
//        $telephone = '3880080800';
//        $email = 'test@test.com';
//        $pesel = 'zamówienia';
//        $city = 'city';
//        $zipCode = '124568';
//        $street = 'street';
//        $home = 'zamówienia';
//        $flat = 'ćęąłńóśźżĄĆĘŁŃÓŚŹŻ';
//        $name = 'Vasya';
//        $surname = 'Pupkin';
//        $sku = 'Bundle Prodyct';
//        $employee = 'employee';
//        $salt = Mage::helper('tim_tauron')->getSalt();
//        $md5 = md5($salt . $businessId . $telephone . $email . $pesel . $city . $zipCode . $street . $home . $flat . $name . $surname . $sku . $employee);
//        $requestString = 'businessId=' . $businessId . '&telephone=' . $telephone . '&email=' . $email . '&pesel=' . $pesel . '&city=' . $city . '&zipCode=' . $zipCode . '&street=' . $street . '&home=' . $home . '&flat=' . $flat . '&name=' . $name . '&surname=' . $surname . '&sku=' . $sku . '&employee=' . $employee . '&md5=' . $md5 . '/';
//        $encodedString = base64_encode($requestString);
//        $url = Mage::getUrl("tim_tauron/cart/decode", array('request' => $encodedString));
//
//        $this->_redirectUrl($url);
//    }

    /**
     * Decode data from url string, check and send it to checkout/cart.
     * Show popup window if something wrong.
     */
    public function decodeAction()
    {
        $data = '';
        $requestData = array();
        $request = $this->getRequest()->getParam('request');
        $decodedRequest = rawurldecode(base64_decode(rawurldecode($request)));
        $decodedRequest = substr($decodedRequest, 1);

        $elems = explode("&", $decodedRequest);
        foreach ($elems as $elem) {
            $items = explode("=", $elem);
            $requestData[$items[0]] = ($items[1]);
        }

        foreach ($requestData as $key => $value) {
            if ($key == 'checksum') {
                continue;
            }
            $data .= $value;
        }
        $salt = Mage::helper('tim_tauron')->getSalt();
        $checkMd5 = strtolower(md5($salt . $data));
        $md5 = strtolower($requestData['checksum']);
        $isOrderExist = Mage::getModel('sales/order')
            ->getCollection()
            ->addFieldToFilter('tim_tauron_order', $requestData['businessid'])
            ->getFirstItem()
            ->getEntityId();

        if ($md5 == $checkMd5 and empty($isOrderExist)) {
            $productCollection = Mage::getModel('catalog/product')
                ->getCollection()
                ->addFieldToFilter('tim_ean', $requestData['sku'])
                ->getFirstItem();
            $requestData['sku'] = $productCollection->getSku();
            $productId = $productCollection->getId();
            if (!$productId) {
                echo 'This product does not exist. Please, check sku!';
            }
            if (Mage::getSingleton('core/session')->getOpenAccess()) {
                Mage::getSingleton('core/session')->unsetData('open_access');
            }
            $_product = Mage::getModel('catalog/product')->load($productId);

            Mage::getSingleton('checkout/session')->clear();

            $cart = Mage::getModel('checkout/cart');
            $cart->init();
            $params = array('product' => $productId, 'qty' => self::QTY);

            $request = new Varien_Object();
            $request->setData($params);

            $cart->addProduct($_product, $request);
            $cart->save();
            Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
            Mage::getSingleton('checkout/session')->setData('tauron_cart', $requestData);
            Mage::getSingleton('core/session')->setData('open_access', true);
            $this->_redirect('checkout/cart');
        } else {
            $attrSetId = (int) Mage::getModel('catalog/product')
                ->getCollection()
                ->addFieldToFilter('tim_ean', $requestData['sku'])
                ->getFirstItem()
                ->getAttributeSetId();
            $attributeSetName = Mage::getModel('eav/entity_attribute_set')->load($attrSetId)->getAttributeSetName();
            if ($attributeSetName === self::LED_ATTR_SET) {
                $this->_redirect(Mage::getStoreConfig('web/default/front') . self::POPUP_2);
            } else {
                $this->_redirect(Mage::getStoreConfig('web/default/front') . self::POPUP_1);
            }
        }
    }
}
