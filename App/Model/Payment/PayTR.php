<?php
class PayTR {
    private $merchant_id;
    private $merchant_key;
    private $merchant_salt;

    public function __construct($merchant_id, $merchant_key, $merchant_salt) {

        $this->merchant_id = $merchant_id;
        $this->merchant_key = $merchant_key;
        $this->merchant_salt = $merchant_salt;
    }

    public function sendPaymentRequest($visitor,$orderData) {
        //print_r($orderData);exit;
        $payment_amount = 0;
        $cartItems = array();
        foreach ($orderData['cartItems'] as $cartItem) {

            $cartTotalPrice = $cartItem['urunfiyat']*$cartItem['urunadet'];
            $cartDiscountedPrice = $cartTotalPrice - $cartItem['indirimmiktari'];
            $cartUnitPrice = $cartDiscountedPrice/$cartItem['urunadet'];

            $payment_amount += $cartDiscountedPrice;

            array_push($cartItems, array($cartItem['sayfaad'],$cartUnitPrice,$cartItem['urunadet']));

            //Log::write("PAYTR IFRAME cartItem: (".$payment_amount." | ".$cartTotalPrice." | ".$cartDiscountedPrice.") -            (".$cartUnitPrice." ".$cartItem['urunfiyat'].") - ".$cartItem['urunadet']." - ", "special");

        }

        $user_basket = base64_encode(json_encode($cartItems));

        $languageCode = $orderData['languageCode'];

        $merchant_id = $this->merchant_id;
        $merchant_oid = $orderData['orderUniqID'];

        $merchant_ok_url = $orderData['orderReturnUrl'].'?paymentResult=success';
        $merchant_fail_url = $orderData['orderReturnUrl'].'?paymentResult=fail';

        $user_ip = $orderData['siparisip'];
        $email = $orderData['siparisteslimateposta'];
        $user_name = $visitor['visitorIsMember']['memberFirstName'].' '.$visitor['visitorIsMember']['memberLastName'];

        $visitorAddressCountryCode = $orderData['siparisteslimatadresulkekod'];
        $user_phone = "+$visitorAddressCountryCode".$orderData['siparisteslimatgsm'];

        $user_address = $orderData['cargoAddress'];

        //$payment_amount küsüratı iki hane yapalım
        $payment_amount = number_format($payment_amount, 2, '.', '');
        $payment_amount = $payment_amount*100;


        //$currency = $orderData['currencyCode'];
        $currency = "TRY";

        $no_installment = 0;
        $max_installment = 0;

        $test_mode = 0;
        $debug_on = 1;
        $timeout_limit = 30;

        $hash_str = $merchant_id .$user_ip .$merchant_oid .$email .$payment_amount .$user_basket.$no_installment.$max_installment.$currency.$test_mode;

        //echo("<br>$merchant_id <br>$user_ip <br>$merchant_oid <br>$email <br>$payment_amount <br>$user_basket <br>$no_installment <br>$max_installment <br>$currency <br>$test_mode");

        $paytr_token=base64_encode(hash_hmac('sha256',$hash_str.$this->merchant_salt,$this->merchant_key,true));

        $post_vals=array(
            'merchant_id'=>$merchant_id,
            'user_ip'=>$user_ip,
            'merchant_oid'=>$merchant_oid,
            'email'=>$email,
            'payment_amount'=>$payment_amount,
            'paytr_token'=>$paytr_token,
            'user_basket'=>$user_basket,
            'debug_on'=>$debug_on,
            'no_installment'=>$no_installment,
            'max_installment'=>$max_installment,
            'user_name'=>$user_name,
            'user_address'=>$user_address,
            'user_phone'=>$user_phone,
            'merchant_ok_url'=>$merchant_ok_url,
            'merchant_fail_url'=>$merchant_fail_url,
            'timeout_limit'=>$timeout_limit,
            'currency'=>$currency,
            'test_mode'=>$test_mode,
            'lang'=>$languageCode
        );

        //print_r($post_vals);exit();

        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1) ;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        //ssl sorununu çözelim
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


        $result = @curl_exec($ch);

        if(curl_errno($ch)) {
            Log::write("PAYTR IFRAME connection error. err:".curl_error($ch), "special");
            return false;
        }

        curl_close($ch);

        $result=json_decode($result,1);

        if($result['status']=='success') {
            return $result['token'];
        } else {
            //print_r($result);exit();
            Log::write("PAYTR IFRAME failed. Status: ".$result['status']." - reason:".$result['reason'], "special");
            return false;
        }
    }

    public function checkPaymentStatus($merchant_oid) {

        $merchant_id = $this->merchant_id;
        $merchant_key = $this->merchant_key;
        $merchant_salt = $this->merchant_salt;

        $paytr_token = base64_encode(hash_hmac('sha256', $merchant_id . $merchant_oid . $merchant_salt, $merchant_key, true));

        $post_vals = array('merchant_id' => $merchant_id,
            'merchant_oid' => $merchant_oid,
            'paytr_token' => $paytr_token);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/durum-sorgu");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 90);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $result = @curl_exec($ch);

        if (curl_errno($ch)) {
            echo curl_error($ch);
            curl_close($ch);
            exit;
        }
        curl_close($ch);

        return json_decode($result, 1);

        /*if ($result['status'] != 'success') {

            Log::write("PAYTR Check Order failed. Status: ".$result['status']." - reason:".$result['reason'], "error");
            echo $result['err_no'] . " - " . $result['err_msg'];
            exit;
        }*/
    }

    public function refundPayment($merchant_oid, $return_amount, $reference_no="") {
        $merchant_id = $this->merchant_id;
        $merchant_key = $this->merchant_key;
        $merchant_salt = $this->merchant_salt;

        $paytr_token = base64_encode(hash_hmac('sha256', $merchant_id . $merchant_oid . $return_amount . $merchant_salt, $merchant_key, true));

        $post_vals = array('merchant_id' => $merchant_id,
            'merchant_oid' => $merchant_oid,
            'return_amount' => $return_amount,
            'paytr_token' => $paytr_token,
            'reference_no' => $reference_no);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/iade");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 90);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $result = @curl_exec($ch);

        if (curl_errno($ch)) {
            echo curl_error($ch);
            curl_close($ch);
            exit;
        }

        curl_close($ch);

        return json_decode($result, 1);
    }
}