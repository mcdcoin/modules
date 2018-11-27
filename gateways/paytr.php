<?php
/* 
*** Coded By AydınWeb Yazılım For PayTR ***
*/

function paytr_nolocalcc() {}
function paytr_config() {
$configarray = array(
"FriendlyName" => array(
"Type" => "System",
"Value" => "PayTR"
),
"merchantId" => array("FriendlyName" => "İşyeri Numarası", "Type" => "text", "Size" => "80", ),
"merchantKey" => array("FriendlyName" => "İşyeri Parolası", "Type" => "text", "Size" => "80", ),
"merchantSalt" => array("FriendlyName" => "İşyeri Gizli Anahtarı", "Type" => "text", "Size" => "80", ),
);
return $configarray;
}
	function paytr_activate() {
	}
	function paytr_link($params)
	{
		ini_set('display_errors', 0); error_reporting(0);
		if( isset( $_SERVER["HTTP_CLIENT_IP"] ) ) {
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		} elseif( isset( $_SERVER["HTTP_X_FORWARDED_FOR"] ) ) {
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} else {
			$ip = $_SERVER["REMOTE_ADDR"];
		}

		$basket = base64_encode(json_encode([
			[$params['description'], $params['amount'], 1]
		]));
		$amount=str_replace(['.',','],null,$params['amount']);
		$merchant_oid = uniqid().'PAYTRWHMCS'.$params['invoiceid'];
		
		$paytr_token=base64_encode(hash_hmac('sha256',$params['merchantId'] .$ip .$merchant_oid .$params['clientdetails']['email'] .$amount .$basket.'09'.$params['merchantSalt'],$params['merchantKey'],true));
		
		return '<form method="post" action="https://www.paytr.com/odeme/guvenli">
		<input type="hidden" name="merchant_id" value="'.$params['merchantId'].'">
		<input type="hidden" name="user_ip" value="'.$ip.'">
		<input type="hidden" name="merchant_oid" value="'.$merchant_oid.'">
		<input type="hidden" name="email" value="'.$params['clientdetails']['email'].'">
		<input type="hidden" name="payment_amount" value="'.$amount.'">
		<input type="hidden" name="no_installment" value="0">
		<input type="hidden" name="max_installment" value="9">
		<input type="hidden" name="user_name" value="'.$params['clientdetails']['fullname'].'">
		<input type="hidden" name="user_address" value="'.$params['clientdetails']['address1'].'">
		<input type="hidden" name="user_phone" value="'.$params['clientdetails']['phonenumber'].'">
		<input type="hidden" name="merchant_ok_url" value="' . $params['returnurl'].'">
		<input type="hidden" name="merchant_fail_url" value="' . $params['returnurl'].'">
		<input type="hidden" name="user_basket" value="'.$basket.'">
		<input type="hidden" name="paytr_token" value="'.$paytr_token.'">
		<input type="hidden" name="debug_on" value="1">
		<input type="submit" value="'.$params['langpaynow'].'"></form>';
	}

	if (!defined( 'WHMCS' )) {
	exit( 'This file cannot be accessed directly' );
	}

?>