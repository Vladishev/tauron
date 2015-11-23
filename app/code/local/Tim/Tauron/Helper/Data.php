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
}