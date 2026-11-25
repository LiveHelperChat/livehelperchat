<?php 

class erLhcoreClassTranslateGoogle {
    
        public static function executeRequest($url, $bodyPayload = '', $referer = '')
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            if ($bodyPayload != '') {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyPayload);
                curl_setopt($ch, CURLOPT_HTTPHEADER,
                    array(
                        'Content-Type:application/json'
                    )
                );
            }

            if ($referer != '') {
                curl_setopt($ch, CURLOPT_REFERER, $referer);
            }

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
        public static function detectLanguage($apiKey, $text, $referer = '')
        {  
            if (empty($text)){
                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Missing text to translate'));
            }
            
            $url = "https://www.googleapis.com/language/translate/v2/detect?key={$apiKey}&q=".urlencode($text);
                                
            $rsp = self::executeRequest($url,'', $referer);
            
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
        public static function translate($apiKey, $word, $from, $to, $referer = '')
        {
            $postParams = [
                'target' => $to,
                'q' => []
            ];

            if (is_array($word)) {
                foreach ($word as $wordItem) {
                    $postParams['q'][] = $wordItem['source'];
                }
            } else {
                $postParams['q'][] = $word;
            }

            $url = "https://www.googleapis.com/language/translate/v2?key={$apiKey}";

            $rsp = self::executeRequest($url, json_encode($postParams), $referer);

            $data = json_decode($rsp,true);
            
            if (isset($data['data']['translations'][0]['translatedText'])) {

                if (is_array($word)) {

                    foreach ($data['data']['translations'] as $index => $translationData) {
                        if (isset($translationData['translatedText'])) {
                            $word[$index]['target'] = html_entity_decode($translationData['translatedText'],ENT_QUOTES);
                        }
                    }

                    $errors = array();
                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('translate.after_google_translate', array('word' => & $word, 'errors' => & $errors));
                    if(!empty($errors)) {
                        throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Could not translate').' - '.implode('; ', $errors));
                    }

                    return $word;

                } else {
                    $errors = array();
                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('translate.after_google_translate', array('word' => & $word, 'errors' => & $errors));
                    if(!empty($errors)) {
                        throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Could not translate').' - '.implode('; ', $errors));
                    }

                    return html_entity_decode($data['data']['translations'][0]['translatedText'],ENT_QUOTES);
                }
            }

            throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Could not translate').' - '.$rsp);
        }
    }
?>
