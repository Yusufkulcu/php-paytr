<?php

namespace Yufusphp\PaytrPhp;

class Paytr {
    private $merchant_id;
    private $merchant_key;
    private $merchant_salt;
    private $email;
    private $payment_amount;
    private $merchant_oid;
    private $user_name;
    private $user_address;
    private $user_phone;
    private $merchant_ok_url;
    private $merchant_fail_url;
    private $user_basket;
    private $debug = "1";
    private $test_mode = "0";
    private $no_installment;
    private $max_installment = "0";

    public function setMerchantId($merchantId) : Paytr
    {
        $this->merchant_id = $merchantId;
        return $this;
    }

    public function setMerchantKey($merchantKey) : Paytr
    {
        $this->merchant_key = $merchantKey;
        return $this;
    }

    public function setMerchantSalt($merchantSalt) : Paytr
    {
        $this->merchant_salt = $merchantSalt;
        return $this;
    }

    public function setEmail($email) : Paytr
    {
        $this->email = $email;
        return $this;
    }

    public function setPaymentAmount($paymentAmount) : Paytr
    {
        $this->payment_amount = $paymentAmount;
        return $this;
    }

    public function setOrderId($orderId) : Paytr
    {
        $this->merchant_oid = $orderId;
        return $this;
    }

    public function setUserName($userName) : Paytr
    {
        $this->user_name = $userName;
        return $this;
    }

    public function setUserAdress($userAdress) : Paytr
    {
        $this->user_address = $userAdress;
        return $this;
    }

    public function setUserPhone($userPhone) : Paytr
    {
        $this->user_phone = $userPhone;
        return $this;
    }

    public function setMerchantOkUrl($merchantOkUrl) : Paytr
    {
        $this->merchant_ok_url = $merchantOkUrl;
        return $this;
    }

    public function setMerchantFailUrl($merchantFailUrl) : Paytr
    {
        $this->merchant_fail_url = $merchantFailUrl;
        return $this;
    }

    public function setUserBasket(array $userBasket) : Paytr
    {
        $this->user_basket = base64_encode(json_encode($userBasket));
        return $this;
    }

    public function setDebug($debug = 1) : Paytr
    {
        $this->debug = $debug;
        return $this;
    }

    public function setTestMode($testMode = 0) : Paytr
    {
        $this->test_mode = $testMode;
        return $this;
    }

    public function setNoInstallment($noInstallment) : Paytr
    {
        $this->no_installment = $noInstallment;
        return $this;
    }

    public function getIp() {
        if( isset( $_SERVER["HTTP_CLIENT_IP"] ) ) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif( isset( $_SERVER["HTTP_X_FORWARDED_FOR"] ) ) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else {
            $ip = $_SERVER["REMOTE_ADDR"];
        }
        return $ip;
    }

    public function getForm() {

        $hash_str = $this->merchant_id .$this->getIp() .$this->merchant_oid .$this->email .$this->payment_amount .$this->user_basket.$this->no_installment."0"."TL".$this->test_mode;
        $paytr_token=base64_encode(hash_hmac('sha256',$hash_str.$this->merchant_salt,$this->merchant_key,true));
        $post_vals=array(
            'merchant_id'=>$this->merchant_id,
            'user_ip'=>$this->getIp(),
            'merchant_oid'=>$this->merchant_oid,
            'email'=>$this->email,
            'payment_amount'=>$this->payment_amount,
            'paytr_token'=>$paytr_token,
            'user_basket'=>$this->user_basket,
            'debug_on'=>$this->debug,
            'no_installment'=>$this->no_installment,
            'max_installment'=>"0",
            'user_name'=>$this->user_name,
            'user_address'=>$this->user_address,
            'user_phone'=>$this->user_phone,
            'merchant_ok_url'=>$this->merchant_ok_url,
            'merchant_fail_url'=>$this->merchant_fail_url,
            'timeout_limit'=>"30",
            'currency'=>"TL",
            'test_mode'=>$this->test_mode
        );

        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1) ;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        $result = @curl_exec($ch);

        if(curl_errno($ch)) {
            return ["status"=>"error","text"=>"PAYTR IFRAME connection error","error"=>curl_error($ch)];
        }else {
            curl_close($ch);
            $result=json_decode($result,true);
            if ($result['status']=="success") {
                $token=$result['token'];
                ?>
                <script src="https://www.paytr.com/js/iframeResizer.min.js"></script>
                <iframe src="https://www.paytr.com/odeme/guvenli/<?php echo $token;?>" id="paytriframe" frameborder="0" scrolling="no" style="width: 100%;"></iframe>
                <script>iFrameResize({},'#paytriframe');</script>
                <?php
            }else {
                return ["status"=>"error","text"=>"PAYTR IFRAME connection error","error"=>$result];
            }
        }
    }


    public function checkPayment() {
        global $_POST;
        $post = $_POST;
        $hash = base64_encode( hash_hmac('sha256', $post['merchant_oid'].$this->merchant_salt.$post['status'].$post['total_amount'],  $this->merchant_key, true) );
        if( $hash != $post['hash'] ) die('PAYTR notification failed: bad hash');
        if ($post['status'] == 'success') {
            return ["status"=>"ok","text"=>"PAYTR payment success"];
        }else {
            return ["status"=>"error","text"=>"PAYTR payment failed.","errorcode"=>$post['failed_reason_code'],"errormsg"=>$post['failed_reason_msg']]
        }
        echo "OK";
    }


}
