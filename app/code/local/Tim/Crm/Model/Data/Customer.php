<?php

class Tim_Crm_Model_Data_Customer extends Tim_Crm_Model_Data_Abstract
{
    protected $guid;
    
    
    static protected $addressParams = 
         array(
            'headquarter' => array('categoryName' => 'Siedziba','model' => 'getPrimaryBillingAddress','sufix' => '01','defaultAddress' => 'y-n-n-y-n'),
//            'correspondence' => array('categoryName' => 'Korespondencyjny','model' => 'getPrimaryCorrespondenceAddress','sufix' => '02','defaultAddress' => 'n-n-y-n-y'),
            'delivery' => array('categoryName' => 'Dodatkowy','model' => 'getPrimaryShippingAddress','sufix' => '03','defaultAddress' => 'n-y-n-n-n'),
            ); 
    
    
    public function getGuid()
    {
        if(!$this->guid){
            $this->guid = $this->customer->getTimGuidCustomer(); //@todo change name
        }
        return $this->guid;
    }
    
    public function setGuid($guid)
    {
        $this->guid = $guid;
        return $this;
    }
    
    public function isError()
    {
        $areas = $this->soapAreas;
        if (sizeof($areas) == 1) {
            foreach($areas['DataArea']->PartyMaster->Description as $description) {
                $bodFailureMessage = simplexml_load_string($description);
                foreach($bodFailureMessage->ErrorProcessMessage as $msg) {
                    $this->error['message'] = (string) $msg->Description;
                    $this->error['webservice'] = ''; // put all WS here
                    return true;
                }
            }
        }

        return false;
    }
    
    public function updateCustomer()
    {
        if(!$this->getCustomer()){
            //wyślij email z błędem
            die;
        }
        
        
        $this->getCustomer()->setTimMfgId( $this->getParam('DataArea/PartyMaster/PartyIDs/ID',
                array('name' => 'MFGID','@value' => '@value','@attributes' => 'schemeAgencyName')) );
        $this->getCustomer()->setTimCrmIdCustomer( $this->getParam('DataArea/PartyMaster/PartyIDs/ID',
                array('name' => 'CRMID','@value' => '@value','@attributes' => 'schemeAgencyName')) );
        $this->getCustomer()->setTimGuidCustomer( $this->getParam('DataArea/PartyMaster/PartyIDs/ID',
                array('name' => 'B24ID','@value' => '@value','@attributes' => 'schemeAgencyName')) );
        $this->getCustomer()->setTimAccountNumber( $this->getParam('DataArea/PartyMaster/Contact',
                array('name' => 'IRB','@value' => 'ID','@attributes' => 'type')) );
        
        foreach(self::$addressParams as $addressParam){
            $this->getCustomer()->{ $addressParam['model'] }()->setTimCrmIdAddress(
                            $this->getParam('DataArea/PartyMaster/Party',
                                array('name' => $addressParam['categoryName'],
                                      '@value' => 
                                            array('PartyIDs/ID',
                                                array('name' => 'CRMID','@value' => '@value','@attributes' => 'schemeAgencyName')
                                            )
                                            ,'@attributes' => 'category'))
                    );
        }
        
        $this->getCustomer()->setTimMainContactCrm(  $this->getParam('DataArea/PartyMaster/Contact',
                array('name' => 'MainContactPerson',
                    '@value' => 
                        array('ID',
                            array('name' => 'CRMID','@value' => '@value','@attributes' => 'schemeAgencyName')
                        )
                    ,'@attributes' => 'type')) );
        
        
//        echo '<pre>';
//        print_r($this->crmReceivedData); die;
//        die;
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
                foreach($data as $childParam){
                    if(is_array($childParam) && $childParam['@attributes'][$param['@attributes']] == $param['name']){
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
    
    
    /*
     *  parts of data
     */
    
    protected function getPartyIds()
    {

        return array('ID' =>
                    array(
                        0 => array(
                            'schemeAgencyName' => 'MFGID',
                            '_' => $this->getCustomer()->getTimMfgId(),
                        ),
                        1 => array(
                            'schemeAgencyName' => 'CRMID',
                            '_' => $this->getCustomer()->getTimCrmIdCustomer(),
                        ),
                        2 => array(
                            'schemeAgencyName' => 'B24ID',
                            '_' => $this->getGuid(),
                        ),
                    ),
                    'TaxID' => str_replace('-', '', trim($this->getCustomer()->getTaxvat()))
                );
    }

    protected function getCustomerShortName($param = false)
    {
        return substr($this->getCustomerFullName($param),0,40);
    }


    protected function getCustomerFullName($param = false)
    {
//        $param = $param ? $param : 'headquarter';
        $model = $param ? $this->getCustomer()->{ self::$addressParams[$param]['model'] }() : $this->getCustomer();
        if($this->getCustomer()->getCustomerType() == 'b2c' || $param == false){
            return $model->getFirstname().' '.$model->getLastname();
        }
        
        return $model->getCompany();
    }
    
    protected function getQuorterAddress($model = false)
    {
        if(!$model){ return array(); }
        
        $modelAddress = $this->getCustomer()->{$model}();
        
        return array(
                'AddressLine' =>
                    array(
                        0 => array(
                            'sequenceName' => 'Street',
                            '_' => $modelAddress->getStreet(1)
                        ),
                        1 => array(
                            'sequenceName' => 'Number',
                            '_' => $modelAddress->getStreet(2)
                        ),
                    )
                    ,
                'CityName' => $modelAddress->getCity(), 
                'CountrySubDivisionCode' => 'nieznane',
                'CountryCode' => 'PL',
                'PostalCode' => $modelAddress->getPostcode() 
            );
        
    }
    
    protected function getDefaultAddress($params = false)
    {
        if(!$params){ return array(); }
        
        $yesOrNot = function ($p){
            if($p == 'y'){ return 'Tak'; }
            if($p == 'n'){ return 'Nie'; }
            return '';
        };
        
        $param = explode('-',$params);
        return array(
                0 => array(
                    'type' => 'DomyślnySiedziba',
                    '_' => $yesOrNot(current($param))
                ),
                1 => array(
                    'type' => 'DomyślnyDostawa',
                    '_' => $yesOrNot(next($param))
                ),
                2 => array(
                    'type' => 'DomyślnyKorespondencyjny',
                    '_' => $yesOrNot(next($param))
                ),
                3 => array(
                    'type' => 'DomyślnyFaktura',
                    '_' => $yesOrNot(next($param))
                ),
                4 => array(
                    'type' => 'DomyślnyFakturaWysyłka',
                    '_' => $yesOrNot(next($param))
                )
            );
    }
    
    protected function getAddress($param = false)
    {
        if(!$param){ return array(); }

        $modelAddress = $this->getCustomer()->{ self::$addressParams[$param]['model'] }();
        if($modelAddress->getTimGuidAddress() == '' || $modelAddress->getTimGuidAddress() == 0){
            $modelAddress->setTimGuidAddress( $guid = (string)(rand ( 10 , 99 )).self::$addressParams[$param]['sufix'] ); //@todo stronger guid
            $modelAddress->save();
        }

        return array(
            'category' => self::$addressParams[$param]['categoryName'],
            'PartyIDs' =>
                array(
                    'ID' => 
                        array(
                            0 => 
                                array(
                                    'schemeAgencyName' => 'B24ID',
                                    '_' => $modelAddress->getTimGuidAddress()
                                ),
                            1 => array(
                                'schemeAgencyName' => 'CRMID',
                                '_' => $modelAddress->getTimCrmIdAddress() 
                            ),
                        )
                ),
            'Name' => 'func getCustomerFullName;'.$param,
            'Location' =>
                array(
                    'Address' => 'func getQuorterAddress;'.self::$addressParams[$param]['model'],
                    'Note' => 'func getDefaultAddress;'.self::$addressParams[$param]['defaultAddress']
                )
        );
    }
    
    
    
    protected function getGlobalContact()
    {

        return array(
                'type' => 'Global',
                'Communication' =>
                    array(
                        0 => array(
                            'ChannelCode' => 'Tel gł.',
                            'DialNumber' => $this->getCustomer()->{ self::$addressParams['headquarter']['model'] }()->getTelephone()
                        ),
                        1 => array(
                            'ChannelCode' => 'Tel kom.',
                            'DialNumber' => ''
                        ),
                        2 => array(
                            'ChannelCode' => 'eMail',
                            'URI' => $this->getCustomer()->getEmail()
                        ),
                    )
                );
    }
    
    protected function getMainContactPerson()
    {
        return array(
                    'type' => 'MainContactPerson',
                    'Name' => 
                        array(
                            0 => array(
                                'sequenceName' => 'FirstName',
                                '_' => $this->getCustomer()->{ self::$addressParams['delivery']['model'] }()->getFirstname(),
                            ),
                            1 => array(
                                'sequenceName' => 'LastName',
                                '_' => $this->getCustomer()->{ self::$addressParams['delivery']['model'] }()->getLastname(),
                            ),
                        ),
                    'ID' =>
                        array(
                            0 => array(
                            'schemeAgencyName' => 'B24ID',
                            '_' => $this->getGuid()
                        ),
                            1 => array(
                                    'schemeAgencyName' => 'CRMID',
                                    '_' => $this->getCustomer()->getTimMainContactCrm()
                            )
                        ),
                    'Responsibility' => 'Upoważnienie', //@todo przeanalizować i do poprawki
                    'Communication' =>
                        array(
                            0 => array(
                                'ChannelCode' => 'Tel gł.',
                                'DialNumber' => $this->getCustomer()->{ self::$addressParams['delivery']['model'] }()->getTelephone()
                            ),
                            1 => array(
                                'ChannelCode' => 'Tel kom.',
                                'DialNumber' => ''
                            ),
                            2 => array(
                                'ChannelCode' => 'eMail',
                                'URI' => $this->getCustomer()->getEmail()
                            ),
                        ),
                    );

    }
    
    protected function getIrb()
    {
        return array(
                'type' => 'IRB',
                'ID' => $this->getCustomer()->getTimAccountNumber(),
            );
    }
    
    protected function getCustomerType()
    {
        return array(
                    'type' => 'TypKlienta',
                    '_' => $this->getCustomer()->getCustomerType() == 'b2c' ? 'Osoba' : 'Firma'
                );
    }

    protected function getStructureOfGetCustomerInfo()
    {
        return 
              array('GetPartyMaster' => 
                  array('ApplicationArea' => 'func getApplicationArea'),
                  array('DataArea' => 
                      array(
                          'Get' => array('Expression' => 'Get'),
                          'PartyMaster' => array('PartyIDs' => 'func getPartyIds')
                      )
                  )
              );
          
    }
    
    protected function getStructureOfPushCustomerInfo()
    {
        return 
        array(          
            'SyncPartyMaster' => 
                  array(
                      'releaseID' => 'TIM01',
                      'ApplicationArea' => 'func getApplicationArea',
                      'DataArea' =>
                          array(
                                'Sync' => 'func getSync',
                                'PartyMaster' => 
                                        array(
                                            'PartyIDs' => 'func getPartyIds',
                                            'Name' => 'func getCustomerShortName;headquarter',
                                            'Location' =>
                                                array(
                                                    'Name' => 'func getCustomerFullName;headquarter', //@todo name of person or name of company?
                                                    'Address' => 'func getQuorterAddress;getPrimaryBillingAddress',
                                                ),
                                            'Contact' =>
                                                array(
                                                    0 => 'func getGlobalContact',
                                                    1 => 'func getMainContactPerson',
                                                    2 => 'func getIrb'
                                                ),
                                            'Description' =>
                                                array(
                                                    'type' => 'Waluta',
                                                    '_' => 'PLN'
                                                ),
                                            'Note' => 'func getCustomerType',
                                            'Party' =>
                                                array(
                                                    0 => 'func getAddress;headquarter', /* Siedziba */
                                                    1 => 'func getAddress;delivery', /* Dostawy */
//                                                    2 => 'func getAddress;correspondence', /* Korespondencyjny */
                                                    ),
 
                                )
                        )
                    )
        );
    }
    /*
     * 
     */
}