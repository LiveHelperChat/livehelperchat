<?php 

class erLhcoreClassTranslateBing {
    
        public static function getAccessToken($clientId, $clientSecret)
        {            
            // Get a 10-minute access token for Microsoft Translator API.
            $url = 'https://datamarket.accesscontrol.windows.net/v2/OAuth2-13';
            $postParams = 'grant_type=client_credentials&client_id='.urlencode($clientId).
            '&client_secret='.urlencode($clientSecret).'&scope=http://api.microsofttranslator.com';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url); 
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);  
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $rsp = curl_exec($ch); 
            $rsp = json_decode($rsp);
            $access_token = $rsp->access_token;
       
            return array('at' => $access_token, 'exp' => $rsp->expires_in);
        }
        
        public static function detectLanguage($accessToken, $text)
        {  
            if (empty($text)){
                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Missing text to translate'));
            }
            
            $url = "https://api.cognitive.microsofttranslator.com/detect?api-version=3.0";
            $postParams = json_encode(array('Text' => $text));
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:bearer '.$accessToken));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: '.strlen($postParams)));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $rsp = curl_exec($ch);
            
            $data = json_decode($rsp, true);
                        
            $languageCode = '';

            if(array_key_exists('language', $data[0]) && $data[0]['language'] != ''){
                $languageCode = $data[0]['language'];
            }

            if ($languageCode == ''){
                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Could not detect a language'));
            }
            
            return $languageCode;
        }

        public static function translate($access_token, $word, $from, $to)
        {
            $url = 'https://api.cognitive.microsofttranslator.com/translate?api-version=3.0&from='.$from.'&to='.$to;
            $postParams = json_encode(array('Text' => $word));

            if (empty($word)){
                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Missing text to translate'));
            }
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:bearer '.$access_token));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: '.strlen($postParams)));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);  
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $rsp = curl_exec($ch);

            $data = json_decode($rsp, true);

            if(!array_key_exists('translations', $data[0])){
                throw new Exception($rsp);
            }

            $errors = array();
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('translate.after_bing_translate', array('word' => & $word, 'errors' => & $errors));
            if(!empty($errors)) {
                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Could not translate').' - '.implode('; ', $errors));
            }

            return $data[0]["translations"][0]["text"]; 
        }
    }
?>