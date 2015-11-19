<?php

class Tim_Crm_Adminhtml_ManualController extends Mage_Adminhtml_Controller_action 
{
    protected function _initAction() 
    {
        $this->loadLayout()
         ->_setActiveMenu('cms/tim_opinions/adminhtml_opinions')
         ->_addBreadcrumb(Mage::helper('adminhtml')->__('Crm'), Mage::helper('adminhtml')->__('Crm'));

        return $this;
    }
    
    public function indexAction()
    {
        Mage::getModel('crm/cron')->run();
//        Mage::getModel('crm/actions')->saveCustomer();
//        Mage::getModel('crm/actions')->saveOrder();
        $this->_initAction()->renderLayout();
    }
    
    
    public function readerrormessageAction()
    {
        $orderId = (int)$this->getRequest()->getParam('id',false);
        
        $order = Mage::getModel('sales/order')->load($orderId);
        $orderInfo = unserialize($order->getTimInfo());
        $orderInfoWsRequest = str_replace("></", ">\n</", $orderInfo['ws_request']);
        $orderInfoWsRequest = str_replace("><", ">\n<", $orderInfoWsRequest);
//        $orderInfoWsRequest = str_replace("</", "\</", $orderInfoWsRequest);
        
        $orderInfoWsResponse = str_replace("></", ">\n</", $orderInfo['ws_response']);
        $orderInfoWsResponse = str_replace("><", ">\n<", $orderInfoWsResponse);
//        $orderInfoWsResponse = str_replace("</", "\</", $orderInfoWsResponse);
        
        
        $html = <<<HTML
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
        </head>
        <body style="background:#f9f4c0;">
        <div style="margin:auto;width:800px;font-size:14px;font-family:Verdana, Geneva, sans-serif;">
            <div style="float:left;">
                Zamówienie nr: <strong>{$order->getRealOrderId()}</strong>
            </div>
            <div style="float:right;">
                Data ostatniej próby wysłania do CRM: <strong>{$orderInfo['created_date']}</strong>
            </div>
            <div style="margin:auto;background:#f9c0c0;border:1px solid red;padding:20px;clear:both;">    
                <center>{$orderInfo['message']}</center>
            </div>
            <div style="text-align:right;padding: 5px 0;">
                <a href="#" id="moreLink" style="text-decoration:none;color:#2775cc;font-weight:bold;">Więcej...</a>
            </div>
            <div id="moreContainer" style="display:none;">
<strong>REQUEST:</strong>
                <xmp>
{$orderInfoWsRequest}
                </xmp>
<strong>RESPONSE:</strong>
                <xmp>
{$orderInfoWsResponse}
                </xmp>
            </div>
        </div>
        <script type="text/javascript">
            var a = document.getElementById('moreLink');
            a.addEventListener('click',function(){
                document.getElementById('moreContainer').style.display = 'block';
            });
        </script>
        </body>
        </html>
        
HTML;
        
        echo $html;
        
    }
    
    public function sendtocrmAction()
    {
        $orderId = (int)$this->getRequest()->getParam('id',false);
        $order = Mage::getModel('sales/order')->load($orderId);
        
        $order->setTimSentToCrm(false);
        $order->setTimError(false);
        $order->setTimInfo('');
        $order->save();
        
        if(!Mage::getStoreConfig('tim_crm/cron/enable')){
            if(Mage::getModel('crm/actions')->saveCustomer($order)){
                Mage::getModel('crm/actions')->saveOrder($order);
            }
            $order->setTimSentToCrm(true);
        } 
        $order->save();
//        die;
        $this->_redirectReferer();
    }
    
}
