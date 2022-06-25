<?php

include "../vendor/autoload.php";

use Yufusphp\PaytrPhp\Paytr;

$paytr = new Paytr();

$result = $paytr
    ->setMerchantKey("wPJ7LFeiYgPS2xLx")
    ->setMerchantSalt("iBCBLH9gR5rGBc2Q")
    ->checkPayment();

if (isset($result['status']) && $result['status']=="ok") {
    // Payment Successfull
}else {
    // Payment Failed
    // $result['errorcode'] Get Error Code
    // $result['errormsg'] Get Error Message
}


?>