<?php
namespace LiveHelperChat\mailConv\workers;
#[\AllowDynamicProperties]
class LangWorker
{
    public function perform()
    {
        $db = \ezcDbInstance::get();
        $db->reconnect(); // Because it timeouts automatically, this calls to reconnect to database, this is implemented in 2.52v

        if (isset($this->args['inst_id']) && $this->args['inst_id'] > 0) {
            $cfg = \erConfigClassLhConfig::getInstance();
            $db->query('USE ' . $cfg->getSetting('db', 'database_user_prefix') . $this->args['inst_id']);
        }

        $messageId = $this->args['msg_id'];
        $message = \erLhcoreClassModelMailconvMessage::fetch($messageId);

        if (!($message instanceof \erLhcoreClassModelMailconvMessage)) {
            return null;
        }

        // Language was already detected skip it
        if ($message->lang != '') {
            return null;
        }

        $globalOptions = (array)\erLhcoreClassModelChatConfig::fetch('mailconv_options_general')->data;

        if ($globalOptions['lang_provider'] == 'antoinefinkelsteinlang') {

            $bodyDetect = $message->subject;

            if ($message->alt_body != '') {
                $bodyDetect .= ' ' . $message->alt_body;
            } else {
                $bodyDetect .= ' ' . strip_tags($message->body);
            }

            try {
                $response = self::getRestAPI([
                    'base_url' => $globalOptions['lang_url'],
                    'method' => '_langdetect?pretty',
                    'body_json' => mb_substr($bodyDetect,0,1000)
                ]);

                if (isset($response['languages'][0]['language'])) {

                    $message->lang = (string)$response['languages'][0]['language'];
                    
                    try {
                        $message->updateThis(['update' => ['lang']]);
                    } catch (\Exception $e) {
                        \sleep(5);
                        $db->reconnect();
                        $message->updateThis(['update' => ['lang']]);
                    }


                    if ($message->conversation_id > 0 &&
                        ($conversation = \erLhcoreClassModelMailconvConversation::fetch($message->conversation_id)) instanceof \erLhcoreClassModelMailconvConversation &&
                        $conversation->lang == '') {
                        $conversation->lang = $message->lang;

                        try {
                            $conversation->updateThis(['update' => ['lang']]);
                        } catch (\Exception $e) {
                            \sleep(5);
                            $db->reconnect();
                            $conversation->updateThis(['update' => ['lang']]);
                        }
                    }
                }

            } catch (\Exception $e) {
                if (!empty(self::$lastCallDebug)) {
                    \erLhcoreClassLog::write($e->getMessage() . "\n" . $e->getTraceAsString() . "\n" . \print_r(self::$lastCallDebug, true),
                        \ezcLog::SUCCESS_AUDIT,
                        array(
                            'source' => 'lhc_mailconv_lang',
                            'category' => 'lang_worker',
                            'line' => __LINE__,
                            'file' => __FILE__,
                            'object_id' => $message->id
                        )
                    );
                }
            }
        }
    }

    public static function detectLanguage($message) {

        static $globalOptions = null;

        if ($globalOptions === null) {
            $globalOptions = (array)\erLhcoreClassModelChatConfig::fetch('mailconv_options_general')->data;
        }

        if (isset($globalOptions['active_lang_detect']) && $globalOptions['active_lang_detect'] == true) {

            $workerType = \erConfigClassLhConfig::getInstance()->getSetting( 'webhooks', 'worker' );

            if ($workerType == 'resque' && class_exists('erLhcoreClassExtensionLhcphpresque')) {
                $inst_id = class_exists('\erLhcoreClassInstance') ? \erLhcoreClassInstance::$instanceChat->id : 0;
                \erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_mailconv_lang', '\LiveHelperChat\mailConv\workers\LangWorker', array('inst_id' => $inst_id, 'msg_id' => $message->id));
            } else {
                $langDetect = new self();
                $langDetect->args['msg_id'] = $message->id;
                $langDetect->perform();
            }
        }
    }

    public static function getRestAPI($params)
    {
        $try = isset($params['try']) ? $params['try'] : 1;

        for ($i = 0; $i < $try; $i++) {

            $ch = curl_init();
            $url = rtrim($params['base_url'], '/') . '/' . $params['method'] . (isset($params['args']) ? '?' . http_build_query($params['args']) : '');

            if (!isset(self::$lastCallDebug['request_url'])) {
                self::$lastCallDebug['request_url'] = array();
            }

            if (!isset(self::$lastCallDebug['request_url_response'])) {
                self::$lastCallDebug['request_url_response'] = array();
            }

            self::$lastCallDebug['request_url'][] = $url;

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, self::$apiTimeout);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            if (isset($params['method_type']) && $params['method_type'] == 'delete') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            }

            $headers = array('Accept: application/json');

            if (isset($params['body_json']) && !empty($params['body_json'])) {
                curl_setopt($ch, CURLOPT_POST,1 );
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params['body_json']);
                $headers[] = 'Content-Type: application/json';
                $headers[] = 'Expect:';
            }

            if (isset($params['bearer']) && !empty($params['bearer'])) {
                $headers[] = 'Authorization: Bearer ' . $params['bearer'];
            }

            if (isset($params['headers']) && !empty($params['headers'])) {
                $headers = array_merge($headers, $params['headers']);
            }

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

            $startTime = date('H:i:s');
            $additionalError = ' ';

            if (isset($params['test_mode']) && $params['test_mode'] == true) {
                $content = $params['test_content'];
                $httpcode = 200;
            } else {
                $content = curl_exec($ch);

                if (curl_errno($ch))
                {
                    $additionalError = ' [ERR: '. curl_error($ch).'] ';
                }

                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            }

            $endTime = date('H:i:s');

            if (isset($params['log_response']) && $params['log_response'] == true) {
                self::$lastCallDebug['request_url_response'][] = '[T' . self::$apiTimeout . '] ['.$httpcode.']'.$additionalError.'['.$startTime . ' ... ' . $endTime.'] - ' . ((isset($params['body_json']) && !empty($params['body_json'])) ? $params['body_json'] : '') . ':' . $content;
            }

            if ($httpcode == 204) {
                return array();
            }

            if ($httpcode == 404) {
                throw new \Exception('Resource could not be found!');
            }

            if (isset($params['return_200']) && $params['return_200'] == true && $httpcode == 200) {
                return $content;
            }

            if ($httpcode == 401) {
                throw new \Exception('No permission to access resource!');
            }

            if ($content !== false)
            {
                if (isset($params['raw_response']) && $params['raw_response'] == true){
                    return $content;
                }

                $response = json_decode($content,true);
                if ($response === null) {
                    if ($i == 2) {
                        throw new \Exception('Invalid response was returned. Expected JSON');
                    }
                } else {
                    if ($httpcode != 500) {
                        return $response;
                    }
                }

            } else {
                if ($i == 2) {
                    throw new \Exception('Invalid response was returned');
                }
            }

            if ($httpcode == 500 && $i >= 2) {
                throw new \Exception('Invalid response was returned');
            }

            usleep(300);
        }
    }

    public static $lastCallDebug = array();
    public static $apiTimeout = 10;
}
