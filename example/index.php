<?php

include "../vendor/autoload.php";

use Yufusphp\PaytrPhp\Paytr;

$paytr = new Paytr();

$result = $paytr
->setMerchantId("206706")
->setMerchantKey("wPJ7LFeiYgPS2xLx")
->setMerchantSalt("iBCBLH9gR5rGBc2Q")
->setEmail("rkulcu6@gmail.com")
->setPaymentAmount("100")
->setOrderId(rand())
->setUserName("Yusuf Külcü")
->setUserAdress("çekmece mahallesi 361.sokak")
->setUserPhone("05444705896")
->setUserBasket(["Ürün","1.00","1"])
->setMerchantOkUrl("http://localhost/sanal-pos-k%c3%bct%c3%bcphaneleri/paytr-php/success.php")
->setMerchantFailUrl("http://localhost/sanal-pos-k%c3%bct%c3%bcphaneleri/paytr-php/fail.php")
->setNoInstallment("0")
->getForm();

if (isset($result['error'])) {
    print_r($result);
}


?>