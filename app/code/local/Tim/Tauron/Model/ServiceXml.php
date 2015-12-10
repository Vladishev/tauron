<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Tauron
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Tauron_Model_ServiceXml extends Mage_Core_Model_Abstract
{
    /** @const Path where xml files saved */
    const TAURON_DIR_PATH = '/home/tauron/tauron/';
    private $msg;

    /**
     * Service all XML file from folder TAURON_DIR_PATH
     */
    public function run()
    {
        if (!is_readable(self::TAURON_DIR_PATH)) {
            $this->msg = $this->getHelper()->__('Permission for this folder is %s. Change permission for this folder.', $this->getPerm(self::TAURON_DIR_PATH));
            Mage::log($this->msg, null, 'tim_tauron.log');
            return;
        }
        $fileNames = array_diff(scandir(self::TAURON_DIR_PATH), array('..', '.'));
        $folderName = date('Ymd');
        $pathForSave = Mage::getBaseDir('var') . DS . 'xml' . DS;
        if (!is_dir($pathForSave)) {
            mkdir($pathForSave);
        }
        $folderForSave = $pathForSave . DS . $folderName;
        if (!is_dir($folderForSave)) {
            mkdir($folderForSave);
        }
        foreach ($fileNames as $fileName) {
            $filePath = self::TAURON_DIR_PATH . $fileName;
            if ($this->checkFileExtension($filePath, 'xml')) {
                if (file_exists($filePath)) {
                    $objXml = simplexml_load_file($filePath);
                } else {
                    $this->msg = $this->getHelper()->__('Can\'t open the file: %s.', $filePath);
                    Mage::log($this->msg, null, 'tim_tauron.log');
                }
                $xmlModel = Mage::getModel('tim_tauron/xml')
                    ->setBusinessId($objXml->businessId)
                    ->setTelephone($objXml->telephone)
                    ->setEmail($objXml->email)
                    ->setPesel($objXml->pesel)
                    ->setCity($objXml->city)
                    ->setZipCode($objXml->zipCode)
                    ->setStreet($objXml->street)
                    ->setHome($objXml->home)
                    ->setFlat($objXml->flat)
                    ->setName($objXml->name)
                    ->setSurname($objXml->surname)
                    ->setSku($objXml->sku)
                    ->setEmployee($objXml->employee)
                    ->setUrl($this->getBase64Url($objXml));
                try {
                    $xmlModel->save();
                    if ($this->moveFiles($filePath, $folderForSave)) {
                        //move the same file with .htm file format
                        $filePath = str_replace('.xml', '.htm', $filePath);
                        $this->moveFiles($filePath, $folderForSave);
                    }
                } catch (Exception $e) {
                    Mage::log($e->getMessage(), null, 'tim_tauron.log');
                }
            }
        }
    }

    /**
     * Checks extension of file
     * @param string $pathToFile
     * @param string $extension
     * @return bool
     */
    private function checkFileExtension($pathToFile, $extension)
    {
        $file = pathinfo($pathToFile);
        if (isset($file['extension']) && $file['extension'] == $extension) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Gets file permission
     * @param string $file
     * @return string
     */
    private function getPerm($file)
    {
        return substr(decoct(fileperms($file)), 2);
    }

    /**
     * Move file from one directory to another
     * @param string $pathToFile
     * @param string $folderName
     * @return bool
     */
    private function moveFiles($pathToFile, $folderName)
    {
        $file = pathinfo($pathToFile);
        try {
            if (file_exists($pathToFile)) {
                rename($pathToFile, $folderName . DS . $file['basename']);
                return true;
            }
        } catch (Exception $e) {
            $this->getHelper()->__('File: %s was not moved.', $pathToFile);
            Mage::log(($this->msg), null, 'tim_tauron.log');
        }
        return false;
    }

    /**
     * Send reminder email to customer from table tim_tauron_xml
     */
    public function sendReminderEmailToCustomer()
    {
        $collection = Mage::getModel('tim_tauron/xml')->getCollection()->getData();
        foreach ($collection as $item) {
            $templateVars = array();
            $email = $item['email'];
            $templateVars['customerName'] = $item['name'] . ' ' . $item['surname'];
            $templateVars['base64Url'] = $item['url'];
            $templateVars['tim_logo_url'] = $this->getHelper()->getLogoUrl('logo_email.gif');
            $subject = Mage::app()->getStore()->getFrontendName() . ': Przypomnienie o złozeniu zamówienia.';
            $template = 'customer_reminder_template';
            $this->getHelper()->sendEmail($email, $templateVars, $subject, $template);
        }
    }

    /**
     * Create base64url for preparing order
     * @param object $objXml
     * @return string
     */
    public function getBase64Url($objXml)
    {
        $businessId = $objXml->businessId;
        $telephone = $objXml->telephone;
        $email = $objXml->email;
        $pesel = $objXml->pesel;
        $city = $objXml->city;
        $zipCode = $objXml->zipcode;
        $street = $objXml->street;
        $home = $objXml->home;
        $flat = $objXml->flat;
        $name = $objXml->name;
        $surname = $objXml->surname;
        $sku = $objXml->sku;
        $employee = $objXml->employee;
        $salt = $this->getHelper()->getSalt();
        $site = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $md5 = md5($salt . $businessId . $telephone . $email . $pesel . $city . $zipCode . $street . $home . $flat . $name . $surname . $sku . $employee);
        $requestString = '?businessId=' . $businessId . '&telephone=' . $telephone . '&email=' . $email . '&pesel=' . $pesel . '&city=' . $city . '&zipcode=' . $zipCode . '&street=' . $street . '&home=' . $home . '&flat=' . $flat . '&name=' . $name . '&surname=' . $surname . '&sku=' . $sku . '&employee=' . $employee . '&checksum=' . $md5;
        $encodedString = rawurlencode(base64_encode(rawurlencode($requestString)));
        $base64Url = $site . "/tim_tauron/cart/decode/request/" . $encodedString;

        return $base64Url;
    }

    private function getHelper()
    {
        return Mage::helper('tim_tauron');
    }
}