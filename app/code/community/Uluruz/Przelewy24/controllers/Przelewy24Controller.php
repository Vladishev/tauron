<?php

class Uluruz_Przelewy24_Przelewy24Controller extends Mage_Core_Controller_Front_Action 
{

    private function p24Weryfikuj($p24_seller_id, $p24_session_id, $p24_order_id, $p24_kwota = "")
	{
		
		$P = array(); $RET = array();
		$url = rtrim(Mage::helper('przelewy24')->getParams()->url).'/transakcja.php';	
		$P[] = "p24_session_id=".$p24_session_id;
		$P[] = "p24_id_sprzedawcy=".$p24_seller_id;
		$P[] = "p24_kwota=".$p24_kwota;
		$P[] = "p24_order_id=".$p24_order_id;
                if(!Mage::helper('przelewy24')->getParams()->test->isAnnonymous){
                    $P[] = "p24_crc=".md5($p24_session_id."|". $p24_order_id."|".$p24_kwota."|".Mage::helper('przelewy24')->getCrcCode()); 
                }
		$user_agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST,1);
		if(count($P)) curl_setopt($ch, CURLOPT_POSTFIELDS,join("&",$P));
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$result=curl_exec ($ch);
		curl_close ($ch);
		$T = explode(chr(13).chr(10),$result);
		$res = false;
		foreach($T as $line)	
		{
			//$line = ereg_replace("[\n\r]","",$line);
			
			$line = str_replace("\n\r","",$line);
			$line = str_replace("\n","",$line);
			$line = str_replace("\r","",$line);

			if($line != "RESULT" and !$res) continue;
			if($res) $RET[] = $line;
			else $res = true;
		}
                return $RET;
	}


	public function redirectAction() 
        {
            $session = Mage::getSingleton('checkout/session');

            $session->setPrzelewyQuoteId($session->getQuoteId());

            $this->getResponse()->setBody($this->getLayout()->createBlock('przelewy24/payment_przelewy24_redirect')->toHtml());
            $session->unsQuoteId();
	}
	
	
	public function successAction() 
        {
		$order_id = Mage::getSingleton('checkout/session')->getLastRealOrderId();
		if($_POST["p24_session_id"]){
                    $sa_sid = explode('|',$_POST["p24_session_id"]);
                    $order_id = $sa_sid[0];
		}

		$order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
		
		$p24_kwota = $order->getTotalDue() * 100; 
		if($p24_kwota == 0){
                    $p24_kwota = $order->getGrandTotal() * 100;	
		}
                
                $p24_session_id = $_POST["p24_session_id"];
                $p24_order_id = $_POST["p24_order_id"];
                $p24_seller_id = Mage::helper('przelewy24')->getParams()->seller_id;
                
		$result = $this->p24Weryfikuj($p24_seller_id,$p24_session_id,$p24_order_id,$p24_kwota);
                                
                if($result[0]=="TRUE")
		{
                    //??
                    $order->addStatusToHistory(Mage_Sales_Model_Order::STATE_PROCESSING, Mage::helper('przelewy24')->__('The payment has been accepted.'));
                    
                    $order->sendOrderUpdateEmail();
                    $payment = $order->getPayment();
                    $payment->setData('transaction_id',$_POST["p24_order_id"]);
                    $payment->addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_ORDER);

                    $order->save();

                    $session = Mage::getSingleton('checkout/session');
                    $session->setQuoteId($session->getPrzelewyQuoteId(true));
                    Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
                    $this->_redirect('checkout/onepage/success');
		}
		else
		{
                    $this->_redirect('przelewy24/przelewy24/failure');
		}
	}
	
	public function failureAction() 
	{
		$order_id = Mage::getSingleton('checkout/session')->getLastRealOrderId();
		
		$order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
		
		if(!$order->getId()) { return FALSE; }
		$p24_error = $_POST["p24_error_code"];
		$p24_order_id = $_POST["p24_order_id"];
		$payment = $order->getPayment();
                $payment->setData('transaction_id',$p24_order_id);
                $payment->addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_ORDER);
		
                //??
                $order->addStatusToHistory(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, Mage::helper('przelewy24')->__('Payment error :.'.$p24_error));
                
		$order->save();
		
		$session = Mage::getSingleton('checkout/session');
		$session->setQuoteId($session->getPrzelewyQuoteId(true));
		$session->addError("Płatność za pomocą serwisu Przelewy24 została zakończona niepowodzeniem.");
		
		$this->_redirect('checkout/cart');
	}
	
}