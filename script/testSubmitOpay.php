<?php
function execPostRequest($url, $data)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

$access_key = "sbxsobmas5j0kyv9uto5";           // require your access key from 1pay
$secret = "rbdkyt790f4yzbfql0rebph6qff3ofl1";               // require your secret key from 1pay
$return_url = "http://localhost/payment/onepay/return";
//http://banhbao.io/payment/onepay/return

$command = 'request_transaction';
$amount = 11000;   // >10000
$order_id = 'test_001';
$order_info = 'test tich hop thanh toan bank charging';

$data = "access_key=".$access_key."&amount=".$amount."&command=".$command."&order_id=".$order_id."&order_info=".$order_info."&return_url=".$return_url;
$signature = hash_hmac("sha256", $data, $secret);
$data.= "&signature=".$signature;
$json_bankCharging = execPostRequest('http://api.1pay.vn/bank-charging/service', $data);
print  $json_bankCharging;
//Ex: {"pay_url":"http://api.1pay.vn/bank-charging/sml/nd/order?token=LuNIFOeClp9d8SI7XWNG7O%2BvM8GsLAO%2BAHWJVsaF0%3D", "status":"init", "trans_ref":"16aa72d82f1940144b533e788a6bcb6"}
$decode_bankCharging=json_decode($json_bankCharging,true);  // decode json
print  $decode_bankCharging;
$pay_url = $decode_bankCharging["pay_url"];
header("Location: $pay_url");