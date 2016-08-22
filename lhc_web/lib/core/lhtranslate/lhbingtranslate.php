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
            
            $url = "http://api.microsofttranslator.com/V2/Http.svc/Detect?text=".urlencode($text);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:bearer '.$accessToken));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $rsp = curl_exec($ch);
            
            //Interprets a string of XML into an object.
            $xmlObj = simplexml_load_string($rsp);
                        
            $languageCode = '';
            
            foreach((array)$xmlObj[0] as $val){
                $languageCode = $val;
            }

            if ($languageCode == ''){
                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Could not detect a language'));
            }
            
            return $languageCode;
        }

        public static function translate($access_token, $word, $from, $to)
        {
            $url = 'http://api.microsofttranslator.com/V2/Http.svc/Translate?text='.urlencode($word).'&from='.$from.'&to='.$to;

            if (empty($word)){
                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Missing text to translate'));
            }
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:bearer '.$access_token));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);  
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $rsp = curl_exec($ch);

            if (strpos($rsp, '<string') === false){
                throw new Exception($rsp);
            }

            $errors = array();
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('translate.after_bing_translate', array('word' => & $word, 'errors' => & $errors));
            if(!empty($errors)) {
                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Could not translate').' - '.implode('; ', $errors));
            }

            preg_match_all('/<string (.*?)>(.*?)<\/string>/s', $rsp, $matches);            
            return htmlspecialchars_decode($matches[2][0]); 
        }
    }
?>