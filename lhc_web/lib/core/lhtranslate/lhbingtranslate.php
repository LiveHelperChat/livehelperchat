<?php 

class erLhcoreClassTranslateBing {
    
        public static function getAccessToken($subscriptionKey, $region)
        {          
            if (empty($region)) {
                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Missing translate region'));
            }
              
            // Get a 10-minute access token for Microsoft Translator API.
            $url = "https://$region.api.cognitive.microsoft.com/sts/v1.0/issueToken?Subscription-Key=".urlencode($subscriptionKey);
            $postParams = '?Subscription-Key='.urlencode($subscriptionKey);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url); 
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);  
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $access_token = curl_exec($ch);
            return array('at' => $access_token, 'exp' => 480); //8 minutes expiration
        }
        
        public static function detectLanguage($access_token, $text)
        {  
            if (empty($text)) {
                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Missing text to translate'));
            }
            
            $url = "https://api.cognitive.microsofttranslator.com/detect?api-version=3.0";
            $postParams = json_encode(array(array('Text' => $text)));
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$access_token,
            						'Content-Type: application/json',
            						'Content-Length: '.strlen($postParams)));
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
            $postParams = json_encode(array(array('Text' => $word)));

            if (empty($word)) {
                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Missing text to translate'));
            }
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$access_token,
            						'Content-Type: application/json',
            						'Content-Length: '.strlen($postParams)));
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
