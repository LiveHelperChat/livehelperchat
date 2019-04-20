<?php 

class erLhcoreClassTranslateYandex {


    public static function detectLanguage($apiKey, $text){
        if (empty($text)){
            throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Missing text to translate'));
        }

        $url = 'https://translate.yandex.net/api/v1.5/tr.json/detect?key='. $apiKey .'';

        $postParams  = 'text='.$text;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: '.strlen($postParams)));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);  
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $rsp = curl_exec($ch);
        
        $data = json_decode($rsp, true);

        if((isset($data['code']) && ($data['code'] == 200) && (isset($data['lang'])))) {
            return $data['lang'];
        } else {
            throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Could not detect language').' - '.$data['code'] . ' ' . $data['message']);
        }
    }


    public static function translate($apiKey, $text, $from, $to){
        if (empty($text)){
            throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Missing text to translate'));
        }

        if($from != '') {
            $url = 'https://translate.yandex.net/api/v1.5/tr.json/translate?lang='. $from .'-'. $to .'&key='. $apiKey .'';
        } else {
            $url = 'https://translate.yandex.net/api/v1.5/tr.json/translate?lang='. $to .'&key='. $apiKey .'';
        }

        $postParams  = 'text='.$text;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);      
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: '.strlen($postParams)));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);  
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $rsp = curl_exec($ch);

        $data = json_decode($rsp, true);

        if((isset($data['code']) && ($data['code'] == 200) && (isset($data['text'][0])))) {
            return $data['text'][0];
        } else {
            throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Could not detect language').' - '.$data['code'] . ' ' . $data['message']);
        }
    }
}
?>