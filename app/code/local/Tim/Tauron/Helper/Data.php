<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Tauron
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Tauron_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @return mixed
     */
    public function getSalt()
    {
        $salt = Mage::getStoreConfig('tim_salt/tim_salt_group/salt');
        return $salt;
    }

    /**
     * Gives data from Configuration->TIM SA->SQL View
     * @return array
     */
    public function getSqlViewData()
    {
        $data = array();
        $data['host'] = Mage::getStoreConfig('tim_sql_viev/tim_sql_viev_group/host');
        $data['login'] = Mage::getStoreConfig('tim_sql_viev/tim_sql_viev_group/login');
        $data['password'] = Mage::getStoreConfig('tim_sql_viev/tim_sql_viev_group/password');
        $data['db_name'] = Mage::getStoreConfig('tim_sql_viev/tim_sql_viev_group/db_name');
        return $data;
    }

    /**
     * Returns emails array from Configuration->Complaint
     * @return array
     */
    public function getComplaintEmails()
    {
        $emails = explode(',', rtrim(Mage::getStoreConfig('tim_complaint/tim_complaint_group/complaint'), ',;'));
        $emails = array_map('trim', $emails);
        return $emails;
    }

    /**
     * Checks is customer exist
     * @param (str)$email
     * @return bool
     */
    public function checkForExistingUser($email)
    {
        $customer = Mage::getModel('customer/customer')
            ->getCollection()
            ->addAttributeToSelect('email')
            ->addAttributeToFilter('email', $email)
            ->getFirstItem();
        if(!is_null($customer['email'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check can guest add products to cart
     * @return bool
     */
    public function checkGuest()
    {
        $openAccess = Mage::getSingleton('core/session')->getOpenAccess();
        if ($openAccess == 1) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Sending email
     * @param (str)$toEmail
     * @param (str)$template
     * @param (arr)$templateVar
     * @param (str)$subject
     */
    public function sendEmail($toEmail, $templateVar, $subject, $template)
    {
        $templateId = $template;
        $emailTemplate = Mage::getModel('core/email_template')->loadDefault($templateId);
        $processedTemplate = $emailTemplate->getProcessedTemplate($templateVar);
        $mail = Mage::getModel('core/email')
            ->setToEmail($toEmail)
            ->setBody($processedTemplate)
            ->setSubject($subject)
            ->setFromName(Mage::getStoreConfig('trans_email/ident_general/name'))
            ->setType('html');

        $mail->send();
    }

    /**
     * Gets customer type from session
     * It sets in app/code/local/Tim/Tauron/controllers/Checkout/OnepageController.php
     * @return bool|mixed
     */
    public function getCustomerType()
    {
        if (Mage::getSingleton('checkout/session')->getData('customer_type')) {
            $customerType = Mage::getSingleton('checkout/session')->getData('customer_type');
            return $customerType;
        } else {
            return false;
        }
    }

    /**
     * Doing CRM module action the same like in
     * app/code/local/Tim/Crm/controllers/Adminhtml/ManualController.php sendtocrmAction()
     * @param (int)$orderId
     */
    public function sendToCrm($orderId)
    {
        $order = Mage::getModel('sales/order')->load($orderId);
        $order->setTimSentToCrm(false);
        $order->setTimError(false);
        $order->setTimInfo('');
        $order->save();
Mage::log('CRM - '.$orderId);
        if(!Mage::getStoreConfig('tim_crm/cron/enable')){
            if(Mage::getModel('crm/actions')->saveCustomer($order)){
                Mage::getModel('crm/actions')->saveOrder($order);
            }
            $order->setTimSentToCrm(true);
        }
        $order->save();
        $this->_redirectReferer();
    }
}