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
//Ex url: http://api.1pay.vn/bank-charging/bank_result.jsp?access_key=l6apnlfseia0ooa12gwp&amount=10000&card_name=Ng%C3%A2n+h%C3%A0ng+TMCP+Ngo%E1%BA%A1i+th%C6%B0%C6%A1ng+Vi%E1%BB%87t+Nam&card_type=VCB&order_id=001&order_info=test+dich+vu&order_type=ND&request_time=2014-12-30T17%3A50%3A11Z&response_code=00&response_message=Giao+dich+thanh+cong&response_time=2014-12-30T17%3A52%3A12Z&signature=eb7aef260a18c835582964e840d63f68b9f84d9704bac7b16c8ff7f1ac9bd0d8&trans_ref=44df289349c74a7d9690ad27ed217094&trans_status=finish


$trans_ref = '3b199a4f83074b2dbd589dcd3d8029f7';
$response_code = '00';

$access_key = "sbxsobmas5j0kyv9uto5";           // require your access key from 1pay
$secret = "rbdkyt790f4yzbfql0rebph6qff3ofl1";               // require your secret key from 1pay
$return_url = "https://localhost/bank_result.php"; // returl url

if($response_code == "00")
{
    $command = "close_transaction";

    $data = "access_key=".$access_key."&command=".$command."&trans_ref=".$trans_ref;
    $signature = hash_hmac("sha256", $data, $secret);
    $data.= "&signature=" . $signature;

    $json_bankCharging = execPostRequest('http://api.1pay.vn/bank-charging/service', $data);

    $decode_bankCharging=json_decode($json_bankCharging,true);  // decode json
    // Ex: {"amount":10000,"trans_status":"close","response_time": "2014-12-31T00:52:12Z","response_message":"Giao dịch thành công","response_code":"00","order_info":"test dich vu","order_id":"001","trans_ref":"44df289349c74a7d9690ad27ed217094", "request_time":"2014-12-31T00:50:11Z","order_type":"ND"}

    $response_message = $decode_bankCharging["response_message"];
    $response_code = $decode_bankCharging["response_code"];
    $amount = $decode_bankCharging["amount"];

    if($response_code == "00")
    {
        print $response_message."-".$amount;
    }
    else
        print $response_message;
}
else
    print $response_message;