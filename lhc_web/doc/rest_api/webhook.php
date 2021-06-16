<?php

// Samples messages for text message from incoming webhook

$bodyPOST = '{"messages":[{"chatId":"vao3oa9p0j14n27ai9569ikxi9ci","computer":"78.60.231.0","chatName":"user","type":"chat","date":"1623310243","body":"gggg","sender":"visitor"}]}';

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_URL, 'https://demo.livehelperchat.com/index.php/webhooks/incoming/chat_apisasdd_sdasdasd');
@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyPOST);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json'
));

$content = curl_exec($ch);

var_dump($content);

?>