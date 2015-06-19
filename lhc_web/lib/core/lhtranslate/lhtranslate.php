<?php

class erLhcoreClassTranslate
{

    /**
     * Fetches bing access token
     */
    public static function getBingAccessToken(& $translationConfig, & $translationData)
    {
        if (! isset($translationData['bing_access_token']) || $translationData['bing_access_token_expire'] < time() + 10) {
            $accessTokenData = erLhcoreClassTranslateBing::getAccessToken($translationData['bing_client_id'], $translationData['bing_client_secret']);
            $translationData['bing_access_token'] = $accessTokenData['at'];
            $translationData['bing_access_token_expire'] = time() + $accessTokenData['exp'];
            
            $translationConfig->value = serialize($translationData);
            $translationConfig->saveThis();
        }
    }

    /**
     * Set's chat language
     *
     * Detects chats languages, operator and visitor and translates recent chat messages
     *
     * @param erLhcoreClassModelChat $chat            
     *
     * @param string $visitorLanguage
     *            | Optional
     *            
     * @param string $operatorLanguage
     *            | Optional
     *            
     * @return void || Exception
     *        
     */
    public static function setChatLanguages(erLhcoreClassModelChat $chat, $visitorLanguage, $operatorLanguage)
    {
        $originalLanguages = array(
            'chat_locale' => $chat->chat_locale,
            'chat_locale_to' => $chat->chat_locale_to
        );
        
        $supportedLanguages = self::getSupportedLanguages(true);
        $db = ezcDbInstance::get();
        $data = array();
        
        if (key_exists($visitorLanguage, $supportedLanguages)) {
            $chat->chat_locale = $data['chat_locale'] = $visitorLanguage;
        } else {
            // We take few first messages from visitor and try to detect language
            $stmt = $db->prepare('SELECT msg FROM lh_msg WHERE chat_id = :chat_id AND user_id = 0 ORDER BY id ASC LIMIT 3 OFFSET 0');
            $stmt->bindValue(':chat_id', $chat->id);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
            foreach ($rows as & $row) {
                $row = preg_replace('#\[translation\](.*?)\[/translation\]#is', '', $row);
            }
            
            $msgText = substr(implode("\n", $rows), 0, 500);
            $languageCode = self::detectLanguage($msgText);
            $chat->chat_locale = $data['chat_locale'] = $languageCode;
        }
        
        if (key_exists($operatorLanguage, $supportedLanguages)) {
            $chat->chat_locale_to = $data['chat_locale_to'] = $operatorLanguage;
        } else { // We need to detect opetator language, basically we just take back office language and try to find a match
            $languageCode = substr(erLhcoreClassSystem::instance()->Language, 0, 2);
            if (key_exists($languageCode, $supportedLanguages)) {
                $chat->chat_locale_to = $data['chat_locale_to'] = $languageCode;
            } else {
                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'We could not detect operator language'));
            }
        }
        
        if ($chat->chat_locale == $chat->chat_locale_to) {
            throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Detected operator and visitor languages matches, please choose languages manually'));
        }
        
        // Because chat data can be already be changed we modify just required fields
        $stmt = $db->prepare('UPDATE lh_chat SET chat_locale_to = :chat_locale_to, chat_locale =:chat_locale WHERE id = :chat_id');
        $stmt->bindValue(':chat_id', $chat->id, PDO::PARAM_INT);
        $stmt->bindValue(':chat_locale_to', $data['chat_locale_to'], PDO::PARAM_STR);
        $stmt->bindValue(':chat_locale', $data['chat_locale'], PDO::PARAM_STR);
        $stmt->execute();
        
        // We have to translate only if our languages are different
        if ($originalLanguages['chat_locale'] != $chat->chat_locale || $originalLanguages['chat_locale_to'] != $chat->chat_locale_to) {
            // And now we can translate all chat messages
            self::translateChatMessages($chat);
        }
        
        return $data;
    }

    /**
     * translations recent chat messages to chat locale
     *
     * @param erLhcoreClassModelChat $chat            
     *
     * @return void || Exception
     *        
     */
    public static function translateChatMessages(erLhcoreClassModelChat $chat)
    {
        
        // Allow callback provide translation config first
        $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('translation.get_config', array());
        if ($response !== false && isset($response['status']) && $response['status'] == erLhcoreClassChatEventDispatcher::STOP_WORKFLOW) {
            $translationData = $response['data'];
        } else {
            $translationConfig = erLhcoreClassModelChatConfig::fetch('translation_data');
            $translationData = $translationConfig->data;
        }
        
        if (isset($translationData['translation_handler']) && $translationData['translation_handler'] == 'bing') {
            
            $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('translation.get_bing_token', array(
                'translation_config' => & $translationConfig,
                'translation_data' => & $translationData
            ));
            if ($response !== false && isset($response['status']) && $response['status'] == erLhcoreClassChatEventDispatcher::STOP_WORKFLOW) {
                // Do nothing
            } else {
                self::getBingAccessToken($translationConfig, $translationData);
            }
            
            // Only last 10 messages are translated
            $msgs = erLhcoreClassModelmsg::getList(array(
                'filter' => array(
                    'chat_id' => $chat->id
                ),
                'limit' => 10,
                'sort' => 'id DESC'
            ));
            
            foreach ($msgs as $msg) {
                
                if ($msg->user_id != - 1) {
                    // Visitor message
                    // Remove old Translation
                    $msg->msg = preg_replace('#\[translation\](.*?)\[/translation\]#is', '', $msg->msg);
                    
                    if ($msg->user_id == 0) {
                        $msgTranslated = erLhcoreClassTranslateBing::translate($translationData['bing_access_token'], $msg->msg, $chat->chat_locale, $chat->chat_locale_to);
                    } else { // Operator message
                        $msgTranslated = erLhcoreClassTranslateBing::translate($translationData['bing_access_token'], $msg->msg, $chat->chat_locale_to, $chat->chat_locale);
                    }
                    
                    // If translation was successfull store it
                    if (! empty($msgTranslated)) {
                        
                        $msgTranslated = str_ireplace(array(
                            '[/ ',
                            'Url = http: //',
                            '[IMG] ',
                            ' [/img]',
                            '[/ url]',
                            '[/ i]',
                            '[Img]'
                        ), array(
                            '[/',
                            'url=http://',
                            '[img]',
                            '[/img]',
                            '[/url]',
                            '[/i]',
                            '[img]'
                        ), $msgTranslated);
                        
                        $msg->msg .= "[translation]{$msgTranslated}[/translation]";
                        $msg->saveThis();
                    }
                }
            }
        } else {
            // Only last 10 messages are translated
            $msgs = erLhcoreClassModelmsg::getList(array(
                'filter' => array(
                    'chat_id' => $chat->id
                ),
                'limit' => 10,
                'sort' => 'id DESC'
            ));
            
            $length = 0;
            
            foreach ($msgs as $msg) {
                if ($msg->user_id != - 1) {
                    // Visitor message
                    // Remove old Translation
                    $msg->msg = preg_replace('#\[translation\](.*?)\[/translation\]#is', '', $msg->msg);
                    
                    if ($msg->user_id == 0) {
                        $msgTranslated = erLhcoreClassTranslateGoogle::translate($translationData['google_api_key'], $msg->msg, $chat->chat_locale, $chat->chat_locale_to);
                    } else { // Operator message
                        $msgTranslated = erLhcoreClassTranslateGoogle::translate($translationData['google_api_key'], $msg->msg, $chat->chat_locale_to, $chat->chat_locale);
                    }
                    
                    $length += mb_strlen($msgTranslated);
                    
                    // If translation was successfull store it
                    if (! empty($msgTranslated)) {
                        
                        $msgTranslated = str_ireplace(array(
                            '[/ ',
                            'Url = http: //',
                            '[IMG] ',
                            ' [/img]',
                            '[/ url]',
                            '[/ i]',
                            '[Img]'
                        ), array(
                            '[/',
                            'url=http://',
                            '[img]',
                            '[/img]',
                            '[/url]',
                            '[/i]',
                            '[img]'
                        ), $msgTranslated);
                        
                        $msg->msg .= "[translation]{$msgTranslated}[/translation]";
                        $msg->saveThis();
                    }
                }
            }
            
            if ($length > 0) {
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('translate.messagetranslated', array(
                    'length' => $length,
                    'chat' => & $chat
                ));
            }
        }
    }

    /**
     * Translation config helper to avoid constant fetching from database
     */
    public static function getTranslationConfig()
    {
        static $config = null;
        
        if ($config === null) {
            $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('translation.get_config', array());
            if ($response !== false && isset($response['status']) && $response['status'] == erLhcoreClassChatEventDispatcher::STOP_WORKFLOW) {
                $config = $response['data'];
            } else {
                $translationConfig = erLhcoreClassModelChatConfig::fetch('translation_data');
                $config = $translationConfig->data;
            }
        }
        
        return $config;
    }

    /**
     * Helper function which returns supported languages by translation provider Bing || Google
     */
    public static function getSupportedLanguages($returnOptions = false)
    {
        $translationData = self::getTranslationConfig();
        $options = array();
        
        if (isset($translationData['translation_handler']) && $translationData['translation_handler'] == 'bing') {
            
            $options['ar'] = 'Arabic';
            $options['bg'] = 'Bulgarian';
            $options['ca'] = 'Catalan';
            $options['zh-CHS'] = 'Chinese Simplified';
            $options['zh-CHT'] = 'Chinese Traditional';
            $options['cs'] = 'Czech';
            $options['da'] = 'Danish';
            $options['nl'] = 'Dutch';
            $options['en'] = 'English';
            $options['et'] = 'Estonian';
            $options['fi'] = 'Finnish';
            $options['fr'] = 'French';
            $options['de'] = 'German';
            $options['ht'] = 'Haitian Creole';
            $options['he'] = 'Hebrew';
            $options['hi'] = 'Hindi';
            $options['mww'] = 'Hmong Daw';
            $options['hu'] = 'Hungarian';
            $options['id'] = 'Indonesian';
            $options['it'] = 'Italian';
            $options['ja'] = 'Japanese';
            $options['tlh'] = 'Klingon';
            $options['tlh-Qaak'] = 'Klingon (pIqaD)';
            $options['ko'] = 'Korean';
            $options['lv'] = 'Latvian';
            $options['lt'] = 'Lithuanian';
            $options['ms'] = 'Malay';
            $options['mt'] = 'Maltese';
            $options['no'] = 'Norwegian';
            $options['fa'] = 'Persian';
            $options['pl'] = 'Polish';
            $options['pt'] = 'Portuguese';
            $options['ro'] = 'Romanian';
            $options['ru'] = 'Russian';
            $options['sk'] = 'Slovak';
            $options['sl'] = 'Slovenian';
            $options['es'] = 'Spanish';
            $options['sv'] = 'Swedish';
            $options['th'] = 'Thai';
            $options['tr'] = 'Turkish';
            $options['uk'] = 'Ukrainian';
            $options['ur'] = 'Urdu';
            $options['vi'] = 'Vietnamese';
            $options['cy'] = 'Urdu';
            $options['yi'] = 'Welsh';
        } elseif (isset($translationData['translation_handler']) && $translationData['translation_handler'] == 'google') {
            $options['af'] = 'Afrikaans';
            $options['sq'] = 'Albanian';
            $options['ar'] = 'Arabic';
            $options['az'] = 'Azerbaijani';
            $options['eu'] = 'Basque';
            $options['bn'] = 'Bengali';
            $options['be'] = 'Belarusian';
            $options['bg'] = 'Bulgarian';
            $options['ca'] = 'Catalan';
            $options['zh-CN'] = 'Chinese Simplified';
            $options['zh-TW'] = 'Chinese Traditional';
            $options['hr'] = 'Croatian';
            $options['cs'] = 'Czech';
            $options['da'] = 'Danish';
            $options['nl'] = 'Dutch';
            $options['en'] = 'English';
            $options['eo'] = 'Esperanto';
            $options['et'] = 'Estonian';
            $options['tl'] = 'Filipino';
            $options['fi'] = 'Finnish';
            $options['fr'] = 'French';
            $options['gl'] = 'Galician';
            $options['ka'] = 'Georgian';
            $options['de'] = 'German';
            $options['el'] = 'Greek';
            $options['gu'] = 'Gujarati';
            $options['ht'] = 'Haitian Creole';
            $options['iw'] = 'Hebrew';
            $options['hi'] = 'Hindi';
            $options['hu'] = 'Hungarian';
            $options['is'] = 'Icelandic';
            $options['id'] = 'Indonesian';
            $options['ga'] = 'Irish';
            $options['it'] = 'Italian';
            $options['ja'] = 'Japanese';
            $options['kn'] = 'Kannada';
            $options['ko'] = 'Korean';
            $options['la'] = 'Latin';
            $options['lv'] = 'Latvian';
            $options['lt'] = 'Lithuanian';
            $options['mk'] = 'Macedonian';
            $options['ms'] = 'Malay';
            $options['mt'] = 'Maltese';
            $options['no'] = 'Norwegian';
            $options['fa'] = 'Persian';
            $options['pl'] = 'Polish';
            $options['pt'] = 'Portuguese';
            $options['ro'] = 'Romanian';
            $options['ru'] = 'Russian';
            $options['sr'] = 'Serbian';
            $options['sk'] = 'Slovak';
            $options['sl'] = 'Slovenian';
            $options['es'] = 'Spanish';
            $options['sw'] = 'Swahili';
            $options['sv'] = 'Swedish';
            $options['ta'] = 'Tamil';
            $options['te'] = 'Telugu';
            $options['th'] = 'Thai';
            $options['tr'] = 'Turkish';
            $options['uk'] = 'Ukrainian';
            $options['ur'] = 'Urdu';
            $options['vi'] = 'Vietnamese';
            $options['cy'] = 'Urdu';
            $options['yi'] = 'Welsh';
        }
        
        if ($returnOptions == true) {
            return $options;
        }
        
        $optionsObjects = array();
        foreach ($options as $key => $option) {
            $std = new stdClass();
            $std->id = $key;
            $std->name = $option;
            $optionsObjects[] = $std;
        }
        
        return $optionsObjects;
    }

    /**
     * Detects language by text
     *
     * @param
     *            string text
     *            
     * @return language code
     *        
     *        
     */
    public static function detectLanguage($text)
    {
        $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('translation.get_config', array());
        if ($response !== false && isset($response['status']) && $response['status'] == erLhcoreClassChatEventDispatcher::STOP_WORKFLOW) {
            $translationData = $response['data'];
        } else {
            $translationConfig = erLhcoreClassModelChatConfig::fetch('translation_data');
            $translationData = $translationConfig->data;
        }
        
        if (isset($translationData['translation_handler']) && $translationData['translation_handler'] == 'bing') {
            
            $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('translation.get_bing_token', array(
                'translation_config' => & $translationConfig,
                'translation_data' => & $translationData
            ));
            if ($response !== false && isset($response['status']) && $response['status'] == erLhcoreClassChatEventDispatcher::STOP_WORKFLOW) {
                // Do nothing
            } else {
                self::getBingAccessToken($translationConfig, $translationData);
            }
            
            return erLhcoreClassTranslateBing::detectLanguage($translationData['bing_access_token'], $text);
        } elseif (isset($translationData['translation_handler']) && $translationData['translation_handler'] == 'google') {
            return erLhcoreClassTranslateGoogle::detectLanguage($translationData['google_api_key'], $text);
        }
    }

    /**
     * Translations provided text from source to destination language
     *
     * @param string $text            
     *
     * @param string $translateFrom            
     *
     * @param string $translateTo            
     *
     *
     */
    public static function translateTo($text, $translateFrom = false, $translateTo)
    {
        $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('translation.get_config', array());
        if ($response !== false && isset($response['status']) && $response['status'] == erLhcoreClassChatEventDispatcher::STOP_WORKFLOW) {
            $translationData = $response['data'];
        } else {
            $translationConfig = erLhcoreClassModelChatConfig::fetch('translation_data');
            $translationData = $translationConfig->data;
        }
        
        if (isset($translationData['translation_handler']) && $translationData['translation_handler'] == 'bing') {
            
            $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('translation.get_bing_token', array(
                'translation_config' => & $translationConfig,
                'translation_data' => & $translationData
            ));
            if ($response !== false && isset($response['status']) && $response['status'] == erLhcoreClassChatEventDispatcher::STOP_WORKFLOW) {
                // Do nothing
            } else {
                self::getBingAccessToken($translationConfig, $translationData);
            }
            
            if ($translateFrom == false) {
                $translateFrom = self::detectLanguage($text);
            }
            
            return erLhcoreClassTranslateBing::translate($translationData['bing_access_token'], $text, $translateFrom, $translateTo);
        } else {
            
            if ($translateFrom == false) {
                $translateFrom = self::detectLanguage($text);
            }
            
            return erLhcoreClassTranslateGoogle::translate($translationData['google_api_key'], $text, $translateFrom, $translateTo);
        }
    }

    /**
     * We translation operator language to visitor language
     *
     * @param erLhcoreClassModelChat $chat            
     *
     * @param erLhcoreClassModelmsg $msg            
     *
     *
     */
    public static function translateChatMsgOperator(erLhcoreClassModelChat $chat, erLhcoreClassModelmsg & $msg)
    {
        try {
            
            // Remove old Translation
            $msg->msg = preg_replace('#\[translation\](.*?)\[/translation\]#is', '', $msg->msg);
            
            $translation = self::translateTo($msg->msg, $chat->chat_locale_to, $chat->chat_locale);
            
            // If translation was successfull store it
            if (! empty($translation)) {
                
                $translation = str_ireplace(array(
                    '[/ ',
                    'Url = http: //',
                    '[IMG] ',
                    ' [/img]',
                    '[/ url]',
                    '[/ i]',
                    '[Img]'
                ), array(
                    '[/',
                    'url=http://',
                    '[img]',
                    '[/img]',
                    '[/url]',
                    '[/i]',
                    '[img]'
                ), $translation);
                
                $msg->msg .= "[translation]{$translation}[/translation]";
            }
        } catch (Exception $e) {}
    }

    /**
     * We translation visitor language to operator language
     *
     * @param erLhcoreClassModelChat $chat            
     *
     * @param
     *            erLhcoreClassModelmsg & $msg
     *            
     *            
     */
    public static function translateChatMsgVisitor(erLhcoreClassModelChat $chat, erLhcoreClassModelmsg & $msg)
    {
        try {
            
            // Remove old Translation
            $msg->msg = preg_replace('#\[translation\](.*?)\[/translation\]#is', '', $msg->msg);
            
            $translation = self::translateTo($msg->msg, $chat->chat_locale, $chat->chat_locale_to);
            
            // If translation was successfull store it
            if (! empty($translation)) {
                
                $translation = str_ireplace(array(
                    '[/ ',
                    'Url = http: //',
                    '[IMG] ',
                    ' [/img]',
                    '[/ url]',
                    '[/ i]',
                    '[Img]'
                ), array(
                    '[/',
                    'url=http://',
                    '[img]',
                    '[/img]',
                    '[/url]',
                    '[/i]',
                    '[img]'
                ), $translation);
                
                $msg->msg .= "[translation]{$translation}[/translation]";
            }
        } catch (Exception $e) {}
    }
}

?>