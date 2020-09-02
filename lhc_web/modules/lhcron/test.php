<?php
/**
 * php cron.php -s site_admin -c cron/test
 *
 * For various testing purposes
 *
 * */

$url = 'https://fcm.googleapis.com/fcm/send';


$fields = array(
    'registration_ids' => array('dux4YVSBUvs:APA91bEv2JNGxmBxPNBlv--v62xsnoyObRt0IHcAOOWFXm4tMyxsbsHcFSLo2crI3hr_YyTwu9vPIU8ibrkOo3msB9fXslMraJoI_iO46pwS6DTfMzT4fUTqdnibcktUNcIO7lglOJCo'),
    'notification'=>array("title"=>"Livehelp Messenger","body"=>"Notification is configured!"),
    'data' => array("click_action"=> "FLUTTER_NOTIFICATION_CLICK","info"=>"Device Registered for notifications!",
        "android_channel_id"=>"lhcmessenger_notification"),
    "priority"=>"high"
);

$headers = array(
    'Authorization: key=AAAAiF8DeNk:APA91bFVHu2ybhBUTtlEtQrUEPpM2fb-5ovgo0FVNm4XxK3cYJtSwRcd-pqcBot_422yDOzHyw2p9ZFplkHrmNXjm8f5f-OIzfalGmpsypeXvnPxhU6Db1B2Z1Acc-TamHUn2F4xBJkP',
    'Content-Type: application/json'
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
$result = curl_exec($ch);

if ($result === FALSE) {
    die('Curl failed: ' . curl_error($ch));
}

curl_close($ch);

return $result;


?>