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
     * Uncomment and put your data for test.
     * After this put http://tauron.local/tim_tauron/cart/ to url field.
     */
    public function indexAction()
    {
        $firstName = 'Vasya';
        $lastName = 'Pupkin';
        $ppeCity = 'Ppe city';
        $ppeZip = 'Ppe zip';
        $ppeStreet = 'Ppe street';
        $ppeFlat = 'Ppe flat';
        $email = 'test@test.com';
        $phone = '3880080800';
        $sku = 'Bundle Prodyct';
        $nip = 'nip';
        $customerId = 'customerId';
        $orderNumber = 'order number';
        $employee = 'employee';
        $salt = Mage::helper('tim_tauron')->getSalt();
        $md5 = md5($firstName . $lastName . $ppeCity . $ppeZip . $ppeStreet . $ppeFlat . $email . $phone . $sku . $nip . $customerId . $orderNumber . $employee . $salt);
        $requestString = 'first_name=' . $firstName . '&last_name=' . $lastName . '&ppe_city=' . $ppeCity . '&ppe_zip=' . $ppeZip . '&ppe_street=' . $ppeStreet . '&ppe_flat=' . $ppeFlat . '&email=' . $email . '&phone=' . $phone . '&sku=' . $sku . '&nip=' . $nip . '&id_customer=' . $customerId . '&order_number=' . $orderNumber . '&employee=' . $employee . '&md5=' . $md5;
        $encodedString = base64_encode($requestString);
        $url = Mage::getUrl("tim_tauron/cart/decode", array('request' => $encodedString));

        $this->_redirectUrl($url);
    }

    /**
     * Decode data from url string, check and send it to checkout/cart.
     * Show popup window if something wrong.
     */
    public function decodeAction()
    {
        $data = '';
        $requestData = array();
        $request = $this->getRequest()->getParam('request');
        $decodedRequest = base64_decode($request);
        parse_str($decodedRequest, $requestData);
        foreach ($requestData as $key => $value) {
            if ($key == 'md5') {
                continue;
            }
            $data .= $value;
        }
        $salt = Mage::helper('tim_tauron')->getSalt();
        $checkMd5 = md5($data . $salt);
        if ($requestData['md5'] == $checkMd5) {
            $productId = Mage::getModel("catalog/product")->getIdBySku($requestData['sku']);
            $qty = '1';
            if (!$productId) {
                echo 'This product does not exist. Please, check sku!';
            }
            if (Mage::getSingleton('core/session')->getCloseAccess()) {
                Mage::getSingleton('core/session')->unsetData('close_access');
            }
            $_product = Mage::getModel('catalog/product')->load($productId);

            $cart = Mage::getModel('checkout/cart');
            $cart->init();

            $params = array('product' => $productId, 'qty' => $qty,);

            $request = new Varien_Object();
            $request->setData($params);

            $cart->addProduct($_product, $request);
            $cart->save();
            Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
            $this->_redirect('checkout/cart');
        } else {
            Mage::getSingleton('core/session')->setData('close_access', true);
            $this->_redirect('home?popup=1');
        }
    }
}
