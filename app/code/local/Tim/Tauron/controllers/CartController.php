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
    public function indexAction()
    {
        $firstName = 'Vasya';
        $lastName = 'Pupkin';
        $ppe = 'ppe';
        $email = 'test@gmail.com';
        $phone = '3880080800';
        $sku = 'P2';
        $nip = 'nip';
        $address = 'address';
        $customerId = 'customerId';
        $employee = 'employee';
        $md5 = md5($firstName.$lastName.$ppe.$email.$phone.$sku.$nip.$address.$customerId.$employee);
        $requestString = 'first_name='.$firstName.'&last_name='.$lastName.'&ppe='.$ppe.'&email='.$email.'&phone='.$phone.'&sku='.$sku.'&nip='.$nip.'&address='.$address.'&id_customer='.$customerId.'&employee='.$employee.'&md5='.$md5;
        $encodedString = base64_encode($requestString);

        $url=Mage::getUrl("tim_tauron/cart/decode", array('request'=>$encodedString));
        $this->_redirectUrl($url);

//        $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'tim_tauron'.DS.'cart'.;
        echo $firstName.$lastName.$ppe.$email.$phone.$sku.$nip.$address.$customerId.$employee;
    }

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
        $checkMd5 = md5($data);
        if ($requestData['md5'] == $checkMd5) {

            $id = '2'; // Replace id with your product id
            $qty = '1'; // Replace qty with your qty
            $_product = Mage::getModel('catalog/product')->load($id);

            $cart = Mage::getModel('checkout/cart');
            $cart->init();

            $params = array(
                'product'=>$id,
                'qty' => $qty,
            );

            $request = new Varien_Object();
            $request->setData($params);

            $cart->addProduct($_product, $request );
            $cart->save();
            Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
            $this->_redirect('checkout/cart');
        } else {
            echo 'ERROR!!!';
        }
//        print_r($data);
    }
}
