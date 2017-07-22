<?php 

class erLhcoreClassTranslateGoogle {
    
        public static function executeRequest($url)
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Some hostings produces wargning...
            return curl_exec($ch);
        }
    
       /*  {
            "data": {
            "detections": [
                [
                {
                    "language": "en",
                    "isReliable": false,
                    "confidence": 0.014598781
                }
                ]
                ]
        }
        } */
        public static function detectLanguage($apiKey, $text)
        {  
            if (empty($text)){
                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Missing text to translate'));
            }
            
            $url = "https://www.googleapis.com/language/translate/v2/detect?key={$apiKey}&q=".urlencode($text);
                                
            $rsp = self::executeRequest($url);
            
            $data = json_decode($rsp,true);
                        
            if (isset($data['data']['detections'][0][0]['language'])){
                return $data['data']['detections'][0][0]['language'];
            };
            
            throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Could not detect language').' - '.$rsp);
        }

        /* {
            "data": {
            "translations": [
            {
                "translatedText": "hallo welt"
            }
            ]
        }
        } */
        public static function translate($apiKey, $word, $from, $to)
        {            
            $url = "https://www.googleapis.com/language/translate/v2?key={$apiKey}&q=".urlencode($word)."&source={$from}&target={$to}&format=text";
            
            $rsp = self::executeRequest($url);
            
            $data = json_decode($rsp,true);
            
            if (isset($data['data']['translations'][0]['translatedText'])){
                $errors = array();
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('translate.after_google_translate', array('word' => & $word, 'errors' => & $errors));
                if(!empty($errors)) {
                    throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Could not translate').' - '.implode('; ', $errors));
                }

                return htmlspecialchars_decode($data['data']['translations'][0]['translatedText']);
            };
            
            throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Could not translate').' - '.$rsp);
        }
    }
?>