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
        $businessId = '0011';
        $telephone = '3880080800';
        $email = 'test@test.com';
        $pesel = 'zamówienia';
        $city = 'city';
        $zipCode = '124568';
        $street = 'street';
        $home = 'zamówienia';
        $flat = 'ćęąłńóśźżĄĆĘŁŃÓŚŹŻ';
        $name = 'Vasya';
        $surname = 'Pupkin';
        $sku = 'Bundle Prodyct';
        $employee = 'employee';
        $salt = Mage::helper('tim_tauron')->getSalt();
        $md5 = md5($salt . $businessId . $telephone . $email . $pesel . $city . $zipCode . $street . $home . $flat . $name . $surname . $sku . $employee);
        $requestString = 'businessId=' . $businessId . '&telephone=' . $telephone . '&email=' . $email . '&pesel=' . $pesel . '&city=' . $city . '&zipCode=' . $zipCode . '&street=' . $street . '&home=' . $home . '&flat=' . $flat . '&name=' . $name . '&surname=' . $surname . '&sku=' . $sku . '&employee=' . $employee . '&md5=' . $md5 . '/';
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
        $checkMd5 = md5($salt . $data);
        $md5 = substr($requestData['md5'], 0, -1);

        $isOrderExist = Mage::getModel('sales/order')
            ->getCollection()
            ->addFieldToFilter('tim_tauron_order', $requestData['businessId'])
            ->getFirstItem()
            ->getEntityId();

        if ($md5 == $checkMd5 and empty($isOrderExist)) {
            $productId = Mage::getModel("catalog/product")->getIdBySku($requestData['sku']);
            $qty = '1';
            if (!$productId) {
                echo 'This product does not exist. Please, check sku!';
            }
            if (Mage::getSingleton('core/session')->getOpenAccess()) {
                Mage::getSingleton('core/session')->unsetData('open_access');
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
            Mage::getSingleton('checkout/session')->setData('tauron_cart', $requestData);
            Mage::getSingleton('core/session')->setData('open_access', true);
            $this->_redirect('checkout/cart');
        } else {
            $this->_redirect('home?popup=1');
        }
    }
}
