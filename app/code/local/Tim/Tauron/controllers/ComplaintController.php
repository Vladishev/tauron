<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Tauron
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Tauron_ComplaintController extends Mage_Core_Controller_Front_Action
{
    public function checkOrderAction()
    {
        $postData = $this->getRequest()->getParams();
        $entityId = Mage::getModel('sales/order')
            ->loadByIncrementId($postData['orderNumber'])
            ->getEntityId();

        $order = Mage::getModel("sales/order")->load($entityId);
        $orderItems = $order->getItemsCollection();
        $i = 0;

        foreach ($orderItems as $item) {
            $productsInfo[$i]['id'] = $item->product_id;
            $productsInfo[$i]['name'] = Mage::getModel("catalog/product")
                ->load($item->product_id)
                ->getName();
            $i++;
        }

        print json_encode($productsInfo);
    }

    /**
     * Saves data from form to tim_complaint table
     * and send information list to emails from Configuration->Complaint
     */
    public function formAction()
    {
        $_helper = Mage::helper('tim_tauron');
        $postData = $this->getRequest()->getParams();
        $entityId = Mage::getModel('sales/order')
            ->loadByIncrementId($postData['orderNumber'])
            ->getEntityId();
        $brokenProducts = '';

        foreach ($postData as $key => $value) {
            if (strripos($key, 'product') === 0) {
                $brokenProducts .= $value . ',';
            }
        }

        $brokenProducts = substr($brokenProducts, 0, -1);
        $complaintModel = Mage::getModel("tim_tauron/complaint")
            ->setOrderId($entityId)
            ->setFirstName($postData['firstName'])
            ->setLastName($postData['lastName'])
            ->setStreet($postData['street'])
            ->setCity($postData['city'])
            ->setZip($postData['code'])
            ->setPhone($postData['telephone'])
            ->setEmail($postData['email'])
            ->setComment($postData['additionalData'])
            ->setBrokenProdId($brokenProducts);
        try {
            $complaintModel->save();
            Mage::getSingleton('core/session')->addSuccess($_helper->__('Your complaint was successfully added! Thank you for feedback!'));
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'tim_tauron.log');
            Mage::getSingleton('core/session')->addError($_helper->__('Can\'t add complaint.'));
        }

        $postData['brokenProducts'] = $brokenProducts;
        $postData['day'] = date('d.m.Y', time());
        $emails = $_helper->getComplaintEmails();
        $subject = 'REKLAMCJA - ' . $postData['orderNumber'];

        foreach ($emails as $email) {
            $_helper->sendEmail($email, $postData, $subject, 'complaint_template');
        }

        $this->_redirectReferer();
    }
}