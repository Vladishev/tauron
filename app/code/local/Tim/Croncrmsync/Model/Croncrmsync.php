<?php

class Tim_Croncrmsync_Model_Croncrmsync extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('croncrmsync/croncrmsync');
    }

    public static function crmsynchronization()
    {
        $collection = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('status', array('neq' => Mage_Sales_Model_Order::STATE_CANCELED))// ->addFieldToFilter('status',array('neq' => 'complete'))
            ->load();
        $whereclouse = '';
        foreach ($collection as $checkstatus) {
            if ($chance_id = $checkstatus->getData('tim_chance_id')) {
                $chance_id = "'" . $chance_id . "/01'";
                $whereclouse = $whereclouse . $chance_id;
            }
        }
        $whereclouse = str_replace("''", "','", $whereclouse);
        //    Mage::log($whereclouse);

        $extratest = self::getMssqlCollection($whereclouse, $collection);
        return TRUE;
    }

    public static function getMssqlCollection($CrmOrderNumbers, $newcollections)
    {
        /*in case of admin panel getsetting*/
        //$general = new General(); 
        //$db = Mage::getStoreConfig('tim_local/settings');
        //var_dump($db);
        $server = Mage::getStoreConfig('tim_sql_view/tim_sql_view_group/host');
        $db['port'] = Mage::getStoreConfig('tim_sql_view/tim_sql_view_group/port');
        $db['user'] = Mage::getStoreConfig('tim_sql_view/tim_sql_view_group/login');
        $db['pass'] = Mage::getStoreConfig('tim_sql_view/tim_sql_view_group/password');
        $database = Mage::getStoreConfig('tim_sql_view/tim_sql_view_group/db_name');
        $dbhandle = mssql_connect($server . ':' . $db['port'], $db['user'], $db['pass']);
        $selected = mssql_select_db($database, $dbhandle);

        // in case of ODBC select  $connection = odbc_connect("Driver={SQL Server};Server=$server;Port=1433;Database=$database;", $db['user'], $db['pass']);
        if (!$selected) {
            die('Sadly Something went wrong while connecting to MSSQL');
        } else {
            $sqltest = "SELECT [Nr_oferty],[Nr_faktury],[Kod_statusu_zlecenia],[Tracking_link],[Termin_platnosci],[Data_wystawienia],[Wartosc_netto],[Wartosc_brutto],[Zaplacona],[Link_FV] FROM [BK_Sales_Order] WHERE Nr_oferty IN ($CrmOrderNumbers)";
            $result = mssql_query($sqltest);

            //   $rs=odbc_exec($connection,$sqltest);  in case of ODBC select
            return self::StatusUpdate($result, $newcollections, $dbhandle);
        }
    }

    public static function InvoiceFileEntity($filename)
    {
        if (file_exists('../opt/FAKTURY_INNE/' . $filename)) {
            return '../opt/FAKTURY_INNE/' . $filename;
        } else {
            return false;
        }
    }

    public static function CheckVisibility($orderid)
    {
        $sql = "Select order_id From tim_fv Where order_id=" . $orderid . "";
        $data = Mage::getSingleton('core/resource')
            ->getConnection('core_read')
            ->fetchAll($sql);
        if (empty($data)) {
            return true;
        } else {
            return false;
        }
    }

    public static function PurifyFileName($filename)
    {
        return substr(strrchr($filename, '\\'), 1);
    }

    public static function InvoiceInfoUpdate($row, $order, $storeid)
    {
//        $write = Mage::getSingleton("core/resource")->getConnection("core_write");
//        $query="ALTER TABLE tim_fv ADD UNIQUE (order_number);";
//        $write->query($query);
        $orderid = $order->getData('entity_id');
        $date = new DateTime($row['Termin_platnosci']);
        $paymentdate = $date->format('Y-m-d H:i:s');
        $date = new DateTime($row['Data_wystawienia']);
        $dateofissue = $date->format('Y-m-d H:i:s');

        $write = Mage::getSingleton("core/resource")->getConnection("core_write");

        if (self::CheckVisibility($orderid)) {
            if ($Pname = self::PurifyFileName($row['Link_FV'])) {
                if (self::InvoiceFileEntity($Pname)) {
                    $query = "insert into tim_fv (order_id,order_number,customer_id,fv_number,paid,nett_price,gross_price,payment_date,date_of_issue,link_fv)" . " values (:orderid, :ordernum, :cid,:factornum, :paid, :nettprice, :grossprice, :paymentdate, :dateofissue, :linkfv)";
                    $binds = array('orderid' => $orderid, 'ordernum' => $row['Nr_oferty'], 'cid' => $order->getCustomerId(), 'factornum' => $row['Nr_faktury'], 'paid' => $row['Zaplacona'], 'nettprice' => $row['Wartosc_netto'], 'grossprice' => $row['Wartosc_brutto'], 'paymentdate' => $paymentdate, 'dateofissue' => $dateofissue, 'linkfv' => $row['Link_FV'],);
                    $write->query($query, $binds);
                    //success email with invoices 
                    self::InvoiceEmailing(self::InvoiceFileEntity($Pname), $order, 'Powiadomienie o wystawieniu faktury');
                } else {
//                    self::InvoiceInspectionEmailing($order, 'Brak pliku powiązanego z linkiem do faktury dostarczonym przez CRM.', true);
                    Mage::log('Brak pliku powiązanego z linkiem do faktury dostarczonym przez CRM.', null, 'tim_tauron.log');
                }
            } else {
//                self::InvoiceInspectionEmailing($order, 'Brak linku do faktury w CRM');
                Mage::log('Brak linku do faktury w CRM', null, 'tim_tauron.log');
            }
        }
    }

    public static function StatusUpdate($query, $newcollection, &$var)
    {
        while ($row = mssql_fetch_array($query, MSSQL_ASSOC)) {

            $row_modified = substr(trim($row['Nr_oferty']), 0, 12);
            foreach ($newcollection as $checkstatus) {
                $statuschecker = $checkstatus->getStatus();
                if ($checkstatus->getData('chance_id') == $row_modified) {
//                    self::InvoiceInfoUpdate($row, $checkstatus, $checkstatus->getStoreId());
                    switch ($row['Kod_statusu_zlecenia']) {
                        /*case 001:
                            //  Mage::log('testing cron status updater'.$checkstatus->getData('status').'001');
                            if ($statuschecker != 'pending') {
                                $checkstatus->setData('status', 'pending')->save();
                                self::StatusUpdateEmailing($checkstatus, 'pending');
                            }
                            break;
                        case 002:
                            //  Mage::log('testing cron status updater'.$checkstatus->getData('status').'002');
                            if ($statuschecker != Mage_Sales_Model_Order::STATE_PROCESSING) {
                                $checkstatus->setData('status', Mage_Sales_Model_Order::STATE_PROCESSING)->save();
                                self::StatusUpdateEmailing($checkstatus, 'processing');
                            }
                            break;
                        case 003:
                            //  Mage::log('testing cron status updater'.$checkstatus->getData('status').'003');
                            if ($statuschecker != Mage_Sales_Model_Order::STATE_HOLDED) {
                                $checkstatus->setData('status', Mage_Sales_Model_Order::STATE_HOLDED)->save();
                                self::StatusUpdateEmailing($checkstatus, 'holded');
                            }
                            break;
                        case 004:
                            //  Mage::log('testing cron status updater'.$checkstatus->getData('status').'004');
                            if ($statuschecker != Mage_Sales_Model_Order::STATE_CANCELED) {
                                $checkstatus->setData('status', Mage_Sales_Model_Order::STATE_CANCELED)->save();
                                self::StatusUpdateEmailing($checkstatus, 'canceled');
                            }
                            break;
                        case 005:
                            //  Mage::log('testing cron status updater'.$checkstatus->getData('status').'005');
                            if ($statuschecker != Mage_Sales_Model_Order::STATE_PROCESSING) {
                                $checkstatus->setData('status', Mage_Sales_Model_Order::STATE_PROCESSING)->save();
                                self::StatusUpdateEmailing($checkstatus, 'processing');
                            }
                            break;
                        case 006:
                            // Mage::log('testing cron status updater'.$checkstatus->getData('status').'006');
                            if ($statuschecker != Mage_Sales_Model_Order::STATE_PROCESSING) {
                                $checkstatus->setData('status', Mage_Sales_Model_Order::STATE_PROCESSING)->save();
                                self::StatusUpdateEmailing($checkstatus, 'processing');
                            }
                            break;*/
                        case 007: {
                            //Mage::log('testing cron status updater'.$checkstatus->getData('status').$checkstatus->getData('entity_id').'007');
                            if ($statuschecker != Mage_Sales_Model_Order::STATE_COMPLETE) {
                                $checkstatus->setData('status', Mage_Sales_Model_Order::STATE_COMPLETE)->save();
//                                self::StatusUpdateEmailing($checkstatus, 'complete');
                            }
                            if ($checkstatus->getData('Tracking_link') == NULL && $row['Tracking_link']) {
                                $checkstatus->setData('Tracking_link', $row['Tracking_link'])->save();
//                                self::StatusUpdateEmailing($checkstatus, 'Tracking_link');
                            }
                            self::InvoiceInfoUpdate($row, $checkstatus, $checkstatus->getStoreId());
                            break;
                        }
                    }
                }
            }
        }
        mssql_close($var);
        return true;
    }

    public static function StatusUpdateEmailing($orderinfo, $demanded_status)
    {

        //condition to test -- to avoid sending emails to customers
        Mage::app()->setCurrentStore($orderinfo->getStoreId());
        $emailTemplate = Mage::getModel('core/email_template');
        $emailTemplate->loadDefault('custom_order_tpl');

        if ($demanded_status == 'complete')
            $emailTemplate->setTemplateSubject('Twoje zamówienie zostało wysłane'); else if ($demanded_status == 'processing')
            $emailTemplate->setTemplateSubject('Twoje zamówienie zostało przekazane do realizacji'); else
            $emailTemplate->setTemplateSubject('Śledzenie przesyłki.');

        // Get General email address (Admin->Configuration->General->Store Email Addresses)
        $salesData['email'] = Mage::getStoreConfig('trans_email/ident_general/email', Mage::app()->getStore($orderinfo->getStoreId()));
        $salesData['name'] = Mage::getStoreConfig('trans_email/ident_general/name', Mage::app()->getStore($orderinfo->getStoreId()));
        $emailTemplate->setSenderName($salesData['name']);
        $emailTemplate->setSenderEmail($salesData['email']);
        $emailTemplateVariables['order'] = $orderinfo;

        if ($demanded_status == 'complete')
            $emailTemplateVariables['status'] = "Twoje zamówienie nr <b>" . $orderinfo->getIncrementId() . '-' . $orderinfo->getChanceId() . "</b> zostało wysłane. Wkrótce otrzymasz link, dzięki któremu będziesz mógł śledzić przesyłkę.";
        if ($demanded_status == 'processing')
            $emailTemplateVariables['status'] = "Przystąpiliśmy do realizacji Twojego zamówienia nr <b>" . $orderinfo->getIncrementId() . '-' . $orderinfo->getChanceId() . "</b> i przygotowania dostawy.";

        $emailTemplate->send($orderinfo->getCustomerEmail(), $orderinfo->getStoreName(), $emailTemplateVariables);
        return true;
    }

    public static function InvoiceInspectionEmailing($orderinfo, $subject, $status = false)
    {
        return 1;
        Mage::app()->setCurrentStore($orderinfo->getStoreId());

        $emailTemplate = Mage::getModel('core/email_template');

        $emailTemplate->loadDefault('invoice_inspection_tpl');

        $emailTemplate->setTemplateSubject($subject);
        // Get Authorities email address (Admin->System->Configuration->CRM Email Addresses)
        $autho_email = Mage::getStoreConfig('tim_crm/settings/email_send_error', Mage::app()->getStore($orderinfo->getStoreId()));

        // Get General email address (Admin->Configuration->General->Store Email Addresses)
        $salesData['email'] = Mage::getStoreConfig('trans_email/ident_general/email', Mage::app()->getStore($orderinfo->getStoreId()));
        $salesData['name'] = Mage::getStoreConfig('trans_email/ident_general/name', Mage::app()->getStore($orderinfo->getStoreId()));
        $emailTemplate->setSenderName($salesData['name']);
        $emailTemplate->setSenderEmail($salesData['email']);

        $emailTemplateVariables['order'] = $orderinfo;
        $emailTemplateVariables['link_status'] = $status;
        if ($autho_email) {
            $recipients = explode(";", $autho_email);
            if (count($recipients)) {
                foreach ($recipients as $recipient) {
                    $emailTemplate->send($recipient, $orderinfo->getStoreName(), $emailTemplateVariables);
                }
            }
        }
        return true;
    }

    public static function InvoiceEmailing($path, $orderinfo, $subject)
    {

        Mage::app()->setCurrentStore($orderinfo->getStoreId());

        $emailTemplate = Mage::getModel('core/email_template');

        $emailTemplate->loadDefault('tim_tauron_invoices_attachment_tpl');

//        $emailTemplate->setTemplateSubject($subject);

        // Get General email address (Admin->Configuration->General->Store Email Addresses)
        $salesData['email'] = Mage::getStoreConfig('trans_email/ident_general/email', Mage::app()->getStore($orderinfo->getStoreId()));
        $salesData['name'] = Mage::getStoreConfig('trans_email/ident_general/name', Mage::app()->getStore($orderinfo->getStoreId()));
        $emailTemplate->setSenderName($salesData['name']);
        $emailTemplate->setSenderEmail($salesData['email']);
        $emailTemplate->getMail()->createAttachment(file_get_contents($path), Zend_Mime::TYPE_OCTETSTREAM, Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64, basename($path));

        $emailTemplateVariables['order'] = $orderinfo;
        $emailTemplateVariables['store_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $emailTemplate->send($orderinfo->getCustomerEmail(), $orderinfo->getStoreName(), $emailTemplateVariables);
        return true;
    }
}