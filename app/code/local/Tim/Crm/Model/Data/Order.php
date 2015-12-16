<?php

class Tim_Crm_Model_Data_Order extends Tim_Crm_Model_Data_Abstract
{
    protected $order;


    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }
    
    public function getOrder()
    {
        return $this->order;
    }
    
    public function isError() 
    {
        $result = $this->getParam('DataArea/Sync/ActionCriteria/ActionExpression',array('name' => 'Error','@value' => '@value','@attributes' => 'actionCode'));

        if(!is_array($result)){
            $this->error['message'] = $result;
            return true;
        }
        return false;
    }
    
    public function updateOrder()
    {
        if(!$this->getOrder()){
            //wyślij email z błędem
            die;
        }
       
        $this->getOrder()->setTimChanceId( $this->getParam('DataArea/SalesOrder/0/SalesOrderHeader/AlternateDocumentID/0/ID',
                        array('name' => 'agencyRole', '@value' => '@value','@attributes' => 'CRM')) );
		
		
    }
    
    
    protected function getParam($path,$param = array())
    {
        if(!$this->crmReceivedData){
            $soapAreas = $this->getSoapAreas();
            /* convert received xml to array */
            $this->crmReceivedData = Mage::helper('crm/XML2Array')->createArray($soapAreas['DataArea']->asXML());
        }
        
        $getParam = function($path,$param,$data,$getParam){
            $path = explode('/',$path);
            foreach($path as $p){
                $data = $data[$p];
            }
 
            if(is_array($data)){
                foreach($data as $key => $childParam){
                    if($key == '@value'){
                        $attributes = next($data);
                        if($attributes[$param['@attributes']] == $param['name']){
                            return $childParam;
                        }
                    }
                    if($childParam && @$childParam['@attributes'][$param['@attributes']] == $param['name']){
                        if(is_array($param['@value'])){
                            return $getParam($param['@value'][0],$param['@value'][1],$childParam,$getParam);
                        }
                        return $childParam[$param['@value']];
                    }
                }
            }
            
            return $data;
        };
        

        return $getParam($path,$param,$this->crmReceivedData,$getParam);
    }
    
    protected function getTimChanceId()
    {
        
    }
    
    protected function getStructureOfSendOrder()
    {
        return
        array(
            'SyncSalesOrder' =>
                    array(
                        'releaseID' => 'TIM001',
                        'ApplicationArea' => 'func getApplicationArea',
                        'DataArea' => 
                            array(
                                'Sync' => 'func getSync;sendOrder',
                                'SalesOrder' => 
                                    array(
                                        0 => 'func getSalesOrderHeader',
                                        1 => 'func getSalesOrderLines'
                                    )
                            )
                    )
        );
    }
    
    protected function getSalesOrderHeader()
    {
        if ($this->getOrder()->getShippingArrivalComments()) {
            $comment = $this->getOrder()->getShippingArrivalComments();
        } else {
            $comment = '';
        }
        return array (
            'SalesOrderHeader' => 
                array (
                    'DocumentID' => 
                        array (
                            'agencyRole' => 'B24ID',
                            'ID' => $this->getOrder()->getId(), /* order id from magento */ //$this->getOrder()->getId()
                        ),
                    'CustomerParty' => 
                        array (
                            'AccountID' => $this->getCustomer()->getTimGuidCustomer(), /* customer GUID */
                            'CustomerAccountID' => $this->getCustomer()->getTimMainContactCrm(), /* main contact CRM ID */
                        ),
                    'Note' => 
                        array (
                            'type' => 'PLA', 
                            'status' => Mage::getModel('crm/payment')->getCrmPaymentCode( $this->getOrder()->getPayment()->getMethod() ),
                        ),
                    'PartialShipmentAllowedIndicator' => 'false', //false
                    'Description' => 
                        array(
                            0 => array (
                                    'type' => 'FakturaVAT_z_wysylka',
                                    '_' => 'TAK', //NIE
                                ),
                            1 => array (
                                    'type' => 'Comment',
                                    '_' => $comment,
                                ),
                        ),
                    'UserArea' => 
                        array (
                            'DebugPayment' => $this->getOrder()->getPayment()->getMethodInstance()->getCode(),
                        ),
                ),
        );
    }
    
    protected function getSalesOrderShipping()
    {
        //$orderDate = $this->getOrder()->getCreatedAt();
        if ($this->getOrder()->getShippingArrivalDate()) {
            $requestedShipDateTime = date('Y-m-d', time($this->getOrder()->getShippingArrivalDate()));
        } else {
            $requestedShipDateTime = current(explode(' ',$this->getOrder()->getGomageDeliverydate()));
        }
        if(!$requestedShipDateTime){
                $requestedShipDateTime = date('Y-m-d', strtotime('now + 2 days')); 
        }
        return array (
                    'RequestedShipDateTime' => $requestedShipDateTime, //@todo add .' 00:00:00'
                    'Note' => 
                        array (
                            'type' => 'TRN',
                            'status' => '003',// $this->getTransportStatusCode('trn'),
                        ),
                    'ShipToParty' => 
                        array (
                            'AccountID' => $this->getCustomer()->getPrimaryShippingAddress()->getTimCrmIdAddress(), /* delivery address CRM ID */
                            'PartyIDs' => 
                                array (
                                  'ID' => $this->getCustomer()->getTimMainContactCrm(), /* main contact CRM ID */
                                ),
                            'UserArea' => 
                                array (
//                                    'DebugCustomerName' => 'Jacek Kiernicki',
//                                    'DebugShippingAddress' => 'Karkonoska 1-3/6F Karpacz',
                                    'DebugShippment' => current(explode('_',$this->getOrder()->getShippingMethod())),
                                ),
                        ),
                );
        
    }
    
    protected function getTransportStatusCode($type)
    {
        $address = Mage::getResourceModel('sales/quote_address_collection')
                    ->addFieldToFilter('quote_id', $this->getOrder()->getQuoteId() )
                    ->addFieldToFilter('address_type', 'shipping' )
                    ->addFieldToSelect('address_id')
                    ->getFirstItem();

        if($address->getAddressId()){
            $rate = Mage::getResourceModel('sales/quote_address_rate_collection')
                    ->addFieldToFilter('address_id', $address->getAddressId() )
                    ->addFieldToSelect('method_description')
                    ->getFirstItem();
            $arr = unserialize($rate->getMethodDescription());
            
            return $arr[$type]; 
        }
        
        return '003';
    }


    protected function getSalesOrderItemHeader($_item)
    {
        return array (
                'ItemID' => 
                    array (
                      'ID' => $_item->getProduct()->getTimCrmId(),
                    ),
                'Description' => 
                    array (
                        0 => 
                            array (
                              'type' => 'Name',
                              '_' => $_item->getProduct()->getName(),
                            ),
                        1 => 
                            array (
                                'type' => 'Type',
                                '_' => 'Produkt',
                            ),
                    ),
            );
    }
    
    protected function getSalesOrderItemBarrelHeader($barrel)
    {
        return array (
                'ItemID' => 
                    array (
                      'ID' => $barrel['crm_id'],
                    ),
            );
    }
    
    protected function getSalesOrderItemCutHeader($_item)
    {
        return array (
                'ItemID' => 
                    array (
                      'ID' => Mage::getStoreConfig('tim_checkout/cut_settings/crmid',$_item->getStoreId()),
                    ),
            );
    }
    
    protected function getSalesOrderItemDescription()
    {
        return array (
                'type' => 'Blokada_Linia',
                '_' => 'true',
            );
    }

    protected function getSalesOrderItemUnitPrice($_item,$type = false)
    {
        $barrelPrice = function($type,$productOptions){
            $options = unserialize($productOptions);
            return $options['info_buyRequest'][$type]['price'];
        };
        
        $discount = function($quote_id,$type){
            $quoteTimSerializedDiscount = Mage::getResourceModel('sales/quote_item_collection')
                                        ->addFieldToFilter('quote_id', $quote_id )
                                        ->addFieldToSelect('tim_serialized_discount');
             foreach(unserialize(current(current($quoteTimSerializedDiscount->getData()))) as $discountInfo){
                 if($discountInfo['type'] == $type){
                     return $discountInfo;
                 }
             }
             return false;
        };
        
        
        $price = $type ? $barrelPrice($type,$_item->getData('product_options')) : $_item->getData('price');
        
        if($this->getOrder()->getCouponCode()){
            $discountInfo = $discount($this->getOrder()->getQuoteId(),$type);
            if($discountInfo){
                $price = $discountInfo['price'];
            }
        }
//        echo '<pre>';
//        var_dump($type);
//        var_dump($price);
//        die;
//        echo '<pre>';
//        var_dump(get_class($_item));
//        $price = $_item->getData('price');
//        var_dump(unserialize($_item->getData('product_options'));
//        var_dump('price: '.$price);
//        var_dump('wolumen: '.$_item->getProduct()->getTimWolumen());
//        var_dump('new price: '.($price / $_item->getProduct()->getTimWolumen() * 1000));
//        die;
        
        
        
        
        
        switch ($_item->getProduct()->getTimJednostkaMiary()){
            case 'km' :
                $price = $price / $_item->getProduct()->getTimWolumen() * 1000;
                break;
            case 'm' :
                $price = $price / $_item->getProduct()->getTimWolumen();
                break;
            default:
                if($_item->getProduct()->getTimWolumen() > 1){
                    $price = $price / $_item->getProduct()->getTimWolumen();
                }
                break;
                
        }
        
        return array (
                'Amount' => 
                    array (
                        'currencyID' => 'PLN',
                        '_' => $price,
                    ),
                'PerQuantity' => '1',
            );
    }
    
    protected function getSalesOrderItemBarrelUnitPrice($barrel)
    {
        return array (
                'Amount' => 
                    array (
                        'currencyID' => 'PLN',
                        '_' => $barrel['deposit'],
                    ),
                'PerQuantity' => '1',
            );
    }
    
    protected function getSalesOrderItemCutUnitPrice($cut)
    {
        return array (
                'Amount' => 
                    array (
                        'currencyID' => 'PLN',
                        '_' => $cut['price'],
                    ),
                'PerQuantity' => '1',
            );
    }
    
    protected function getSalesOrderItemBarrel($_item,$barrel,$key)
    { 
        return array (
                'LineNumber' => $_item->getId().$key.'01', /* id order item in magento */
                'Item' => $this->getSalesOrderItemBarrelHeader($barrel),
                'Description' => $this->getSalesOrderItemDescription(),
                'Quantity' => 
                    array (
                        'unitCode' => 'szt.', //@todo
                        '_' => '1',
                    ),
                'UnitPrice' => $this->getSalesOrderItemBarrelUnitPrice($barrel),
                'TotalAmount' => $barrel['deposit'] * $barrel['qty'],
                'QualifiedAmount' => 
                    array (
                      'Amount' => $barrel['deposit'] * $barrel['qty'] + ($barrel['deposit'] * $barrel['qty'] * 0.23), /* bad JK */
                    ),
                'UserArea' => 
                    array (
                        'DebugType' => 'Produkt',
                        'DebugName' => 'Kaucja za '.$barrel['name'],
                    ),
                'DocumentReference' => 
                    array (
                        'LineNumber' => $_item->getLineNumber(),
                    ),
            );
    }
    
    protected function getSalesOrderItemCut($_item,$cut)
    {
        return array (
                'LineNumber' => $_item->getId().'02', /* id order item in magento */ //@todo
                'Item' => $this->getSalesOrderItemCutHeader($_item),
                'Description' => $this->getSalesOrderItemDescription(),
                'Quantity' => 
                    array (
                        'unitCode' => 'szt.', //@todo
                        '_' => $cut['qty'],
                    ),
                'UnitPrice' => $this->getSalesOrderItemCutUnitPrice($cut),
                'TotalAmount' => $cut['price'] * $cut['qty'],
                'QualifiedAmount' => 
                    array (
                      'Amount' => $cut['price'] * $cut['qty'] + ($cut['price'] * $cut['qty'] * 0.23), /* bad JK */
                    ),
                'UserArea' => 
                    array (
                        'DebugType' => 'Usługa',
                        'DebugName' => $cut['name'],
                    ),
            );
    }
    
    protected function getSalesOrderTransport()
    { 
        
        return array (
                'LineNumber' => $this->getOrder()->getId().'09', /* id order item in magento */ //@todo
                'Item' => array (
                    'ItemID' => 
                        array (
                          'ID' => '0x0000000000008bcc', //@todo
                        ),
                    ),
                'Description' => $this->getSalesOrderItemDescription(), //@todo uporządkować
                'Quantity' => 
                    array (
                        'unitCode' => 'szt.', //@todo
                        '_' => 1,
                    ),
                'UnitPrice' => array (
                    'Amount' => 
                        array (
                            'currencyID' => 'PLN',
                            '_' => $this->getOrder()->getBaseShippingAmount(),
                        ),
                        'PerQuantity' => '1',
                    ),
                'TotalAmount' => $this->getOrder()->getBaseShippingAmount(),
                'QualifiedAmount' => 
                    array (
                      'Amount' => $this->getOrder()->getBaseShippingAmount() + $this->getOrder()->getBaseShippingTaxAmount(), 
                    ),
                'Note' => 
                    array (
                      'type' => 'ReferenceService',
                      'status' => 'TRN',
                    ),
                'UserArea' => 
                    array (
                        'DebugType' => 'Usługa',
                        'DebugName' => 'Transport',
                    )
            );
    }

    protected function getSalesOrderItems()
    {
        
        
        $items = array();
        $itemKey = 0;
        foreach($this->getOrder()->getAllItems() as $_item){
//            if(Mage::helper('checkout/calculator')->isBarrelProduct($_item->getProduct())){
//                $itemKey++;
//                $options = $_item->getProductOptions();
//                $options = $options['info_buyRequest'];
//                if($options['custom']['segments']){
//                    foreach($options['custom']['segments'] as $itemQty){
//                        $itemKey++;
//                        $items[] = $this->getSalesOrderBarrelProductItem('custom',$_item,$itemKey,$itemQty,$options['custom']);
//                    }
//                    $key = 0;
//                    foreach(Mage::helper('crm')->getProductBarrelGroup($options['custom']) as $barrel){
//                        $items[] = $this->getSalesOrderItemBarrel($_item,$barrel,$key++);
//                    }
//                    if(isset($options['custom']['cuts']) && $options['custom']['cuts']['qty']){
//                        $items[] = $this->getSalesOrderItemCut($_item,$options['custom']['cuts']);
//                    }
//                }
//                if($options['clearancesales']['segments']){
//                    foreach($options['clearancesales']['segments'] as $itemQty){
//                        $itemKey++;
//                        $items[] = $this->getSalesOrderBarrelProductItem('clearancesales',$_item,$itemKey,$itemQty,$options['clearancesales']);
//                    }
//                    foreach(Mage::helper('crm')->getProductBarrelGroup($options['clearancesales']) as $barrel){
//                        $items[] = $this->getSalesOrderItemBarrel($_item,$barrel);
//                    }
//                }
//            } else {
            if($_item->getProductType() == "bundle") {}
            else
            {$items[] = $this->getSalesOrderSimpleProductItem($_item);}
//            }
        }
        $items[] = $this->getSalesOrderTransport();
//        print_r($items); die;
        
        return $items;             
    }
    
    protected function getSalesOrderSimpleProductItem($_item)
    {
        $qty = $_item->getData('qty_ordered');
        
        /* dla krążków */
        if($_item->getProduct()->getTimWolumen() > 1 && $_item->getProduct()->getTimJednostkaMiary() == 'km'){
            $qty = $qty * $_item->getProduct()->getTimWolumen() / 1000;
        } else if($_item->getProduct()->getTimWolumen() > 1 && $_item->getProduct()->getTimJednostkaMiary() == 'm'){
            /* dla rur karbowanych */
            $qty = $qty * $_item->getProduct()->getTimWolumen();
        } else if($_item->getProduct()->getTimWolumen() > 1){
            /* dla innych opakowań */
            $qty = $qty * $_item->getProduct()->getTimWolumen();
        }
        
        return array (
                    'LineNumber' => $_item->getId(), /* id order item in magento */
                    'Item' => $this->getSalesOrderItemHeader($_item),
                    'Description' => $this->getSalesOrderItemDescription(),
                    'Quantity' => 
                        array (
                            'unitCode' => $_item->getProduct()->getTimJednostkaMiary(),
                            '_' => $qty,
                        ),
                    'UnitPrice' => $this->getSalesOrderItemUnitPrice($_item),
                    'TotalAmount' => $_item->getData('row_total'),
                    'QualifiedAmount' => 
                        array (
                          'Amount' => $_item->getData('row_total_incl_tax'), //include tax
                        ),
                    'UserArea' => 
                        array (
                            'DebugType' => 'Produkt',
                            'DebugName' => $_item->getProduct()->getName(),
                            'DebugSKU' => $_item->getProduct()->getSku(),
                        )
                );
    }

    protected function getSalesOrderBarrelProductItem($type,$_item,$itemKey,$itemQty = null,$option = array()) 
    {        
        switch ($_item->getProduct()->getTimJednostkaMiary()){
            case 'km' :
                $price = $option['price'];
                $qty = $itemQty / 1000;
                break;
            case 'm' :
                $price = $option['price'];
                $qty = $itemQty;
                break;
            default:
                $qty = $itemQty; 
                if($_item->getProduct()->getTimWolumen() > 1){
                    $price = $option['price'] / $_item->getProduct()->getTimWolumen();
                    $qty = $itemQty * $_item->getProduct()->getTimWolumen(); 
                }
                break;
        }
        $_item -> setLineNumber($_item->getId().$itemKey);
        $tmp = array (
                    'LineNumber' => $_item->getId().$itemKey, /* id order item in magento */
                    'Item' => $this->getSalesOrderItemHeader($_item),
                    'Description' => $this->getSalesOrderItemDescription(),
                    'Quantity' => 
                        array (
                            'unitCode' => $_item->getProduct()->getTimJednostkaMiary(),
                            '_' => $qty,
                        ),
                    'UnitPrice' => $this->getSalesOrderItemUnitPrice($_item,$type),
                    'TotalAmount' => $price * $itemQty,
                    'QualifiedAmount' => 
                        array (
                          'Amount' => round($price * $itemQty + ($price * $itemQty * Mage::helper('crm')->getTaxRate($_item)),2),
                        ),
                    'UserArea' => 
                        array (
                            'DebugType' => 'Produkt',
                            'DebugName' => $_item->getProduct()->getName(),
                            'DebugSKU' => $_item->getProduct()->getSku(),
                        )
                );
//        echo '<pre>';
//        var_dump($tmp); die;
        return $tmp;
    }

    
    protected function getSalesOrderLines()
    {
        return array (
            'SalesOrderHeader' => 'func getSalesOrderShipping',
            'SalesOrderLine' => 'func getSalesOrderItems',
                

        );
    }
}


