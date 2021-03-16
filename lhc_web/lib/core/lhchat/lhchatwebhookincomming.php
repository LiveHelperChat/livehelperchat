<?php

class erLhcoreClassChatWebhookIncoming {

    public static function processEvent($incomingWebhook, array $payload) {

        $conditions = $incomingWebhook->conditions_array;

        if (isset($conditions['main_cond']) && $conditions['main_cond'] != "") {
            $conditionsPairs = explode("||",$conditions['main_cond']);
            foreach ($conditionsPairs as $conditionsPair) {
                $conditionsPairData = explode('=',$conditionsPair);

                if ($conditionsPairData[1] === 'false') {
                    $conditionsPairData[1] = false;
                } elseif ($conditionsPairData[1] === 'true') {
                    $conditionsPairData[1] = true;
                } elseif (strpos($conditionsPairData[1], ',') !== false) {
                    $conditionsPairData[1] = explode(',', $conditionsPairData[1]);
                }

                if ((is_array($conditionsPairData[1]) && !in_array($payload[$conditionsPairData[0]], $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($payload[$conditionsPairData[0]]) && $payload[$conditionsPairData[0]] == $conditionsPairData[1]))) {
                    throw new Exception('Main conditions does not met!' . json_encode($payload));
                }
            }
        }

        $messages = isset($conditions['messages']) && $conditions['messages'] != '' ? $payload[$conditions['messages']] : [$payload];

        if (isset($conditions['messages']) && $conditions['messages'] != '' &&  isset($conditions['message_direct']) && $conditions['message_direct'] == true) {
            $messages = [$messages];
        }

        foreach ($messages as $message) {
            self::processMessage($incomingWebhook, $message, $payload);
        }
    }

    public static function processMessage($incomingWebhook, $payloadMessage, $payloadAll) {

        $conditions = $incomingWebhook->conditions_array;

        $typeMessage = 'unknown';

        if (isset($conditions['msg_cond_attachments']) && $conditions['msg_cond_attachments'] != "") {
            $typeMessage = 'attachments';
            $conditionsPairs = explode("||",$conditions['msg_cond_attachments']);
            foreach ($conditionsPairs as $conditionsPair) {
                $conditionsPairData = explode('=',$conditionsPair);

                if ($conditionsPairData[1] === 'false') {
                    $conditionsPairData[1] = false;
                } elseif ($conditionsPairData[1] === 'true') {
                    $conditionsPairData[1] = true;
                } elseif (strpos($conditionsPairData[1], ',') !== false) {
                    $conditionsPairData[1] = explode(',', $conditionsPairData[1]);
                }

                if ((is_array($conditionsPairData[1]) && !in_array($payloadMessage[$conditionsPairData[0]], $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($payloadMessage[$conditionsPairData[0]]) && $payloadMessage[$conditionsPairData[0]] == $conditionsPairData[1]))) {
                    $typeMessage = 'unknown';
                }
            }

            if ($typeMessage == 'attachments') {
                $msgBody = $conditions['msg_attachments'];
            }
        }

        if ($typeMessage == 'unknown')
        {
            if (isset($conditions['msg_cond_img']) && $conditions['msg_cond_img'] != "") {
                $typeMessage = 'img';
                $conditionsPairs = explode("||",$conditions['msg_cond_img']);
                foreach ($conditionsPairs as $conditionsPair) {
                    $conditionsPairData = explode('=',$conditionsPair);

                    if ($conditionsPairData[1] === 'false') {
                        $conditionsPairData[1] = false;
                    } elseif ($conditionsPairData[1] === 'true') {
                        $conditionsPairData[1] = true;
                    } elseif (strpos($conditionsPairData[1], ',') !== false) {
                        $conditionsPairData[1] = explode(',', $conditionsPairData[1]);
                    }

                    if ((is_array($conditionsPairData[1]) && !in_array($payloadMessage[$conditionsPairData[0]], $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($payloadMessage[$conditionsPairData[0]]) && $payloadMessage[$conditionsPairData[0]] == $conditionsPairData[1]))) {
                        $typeMessage = 'unknown';
                    }
                }

                if ($typeMessage == 'img') {
                    $msgBody = $conditions['msg_img'];
                }
            }
        }

        if ($typeMessage == 'unknown')
        {
            $msgBody = $conditions['msg_body'];

            if (isset($conditions['msg_cond']) && $conditions['msg_cond'] != "") {
                $typeMessage = 'text';
                $conditionsPairs = explode("||",$conditions['msg_cond']);
                foreach ($conditionsPairs as $conditionsPair) {
                    $conditionsPairData = explode('=', $conditionsPair);

                    if ($conditionsPairData[1] === 'false') {
                        $conditionsPairData[1] = false;
                    } elseif ($conditionsPairData[1] === 'true') {
                        $conditionsPairData[1] = true;
                    } elseif (strpos($conditionsPairData[1], ',') !== false) {
                        $conditionsPairData[1] = explode(',', $conditionsPairData[1]);
                    }

                    if ((is_array($conditionsPairData[1]) && !in_array($payloadMessage[$conditionsPairData[0]], $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($payloadMessage[$conditionsPairData[0]]) && $payloadMessage[$conditionsPairData[0]] == $conditionsPairData[1]))) {
                        $typeMessage = 'unknown';
                    }
                }
            } else {
                $typeMessage = 'text';
            }
        }

        // Unknown message type.
        if ($typeMessage == 'unknown') {
            throw new Exception('Message conditions does not met! ' . json_encode($payloadMessage));
        }

        $eChat = erLhcoreClassModelChatIncoming::findOne(array(
            'filter' => array(
                'chat_external_id' => $payloadMessage[$conditions['chat_id']],
                'incoming_id' => $incomingWebhook->id
            )
        ));

        $continueIfHasChat = false;

        if ($eChat !== false && ($chat = $eChat->chat) !== false) {
            $continueIfHasChat = ($chat->status != erLhcoreClassModelChat::STATUS_CLOSED_CHAT) || ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT && !(!isset($conditions['chat_status']) || $conditions['chat_status'] == ""));
        }

        if ($continueIfHasChat == true && $eChat !== false && ($chat = $eChat->chat) !== false ) {
            $renotify = false;

            if ($chat instanceof erLhcoreClassModelChat && $chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {

                if (isset($conditions['chat_status']) && $conditions['chat_status'] == 'active' && $chat->user_id > 0) {
                    $chat->status = erLhcoreClassModelChat::STATUS_ACTIVE_CHAT;
                    $chat->status_sub_sub = 2; // Will be used to indicate that we have to show notification for this chat if it appears on list
                } else {
                    $chat->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;
                    $chat->status_sub_sub = 2; // Will be used to indicate that we have to show notification for this chat if it appears on list

                    if (isset($conditions['reset_op']) && $conditions['reset_op'] == true) {
                        $chat->user_id = 0;
                    }

                    $chat->pnd_time = time();
                    $renotify = true;
                }
            }

            if ($typeMessage == 'img' || $typeMessage == 'attachments') {
                if (isset($conditions['msg_'.$typeMessage.'_download']) && $conditions['msg_'.$typeMessage.'_download'] == true) {
                    $file = self::parseFiles($payloadMessage[$conditions['msg_cond_'.$typeMessage.'_body']], $chat);
                    if (!empty($file)) {
                        $payloadMessage[$conditions['msg_cond_'.$typeMessage.'_body']] = $file;
                    }
                }
            }

            $msg = new erLhcoreClassModelmsg();
            $msg->msg = self::extractMessageBody($msgBody,$payloadMessage);
            $msg->chat_id = $chat->id;
            $msg->user_id = 0;
            $msg->time = time();
            erLhcoreClassChat::getSession()->save($msg);

            $chat->last_user_msg_time = $msg->time;
            $chat->last_msg_id = $msg->id;

            if ($renotify == true) {
                erLhcoreClassChatValidator::setBot($chat, array('msg' => $msg));
            }

            // Create auto responder if there is none
            if ($chat->auto_responder === false) {
                $responder = erLhAbstractModelAutoResponder::processAutoResponder($chat);
                if ($responder instanceof erLhAbstractModelAutoResponder) {
                    $responderChat = new erLhAbstractModelAutoResponderChat();
                    $responderChat->auto_responder_id = $responder->id;
                    $responderChat->chat_id = $chat->id;
                    $responderChat->wait_timeout_send = 1 - $responder->repeat_number;
                    $responderChat->saveThis();

                    $chat->auto_responder_id = $responderChat->id;
                    $chat->auto_responder = $responderChat;
                }
            }

            $chatVariables = $chat->chat_variables_array;

            // Auto responder if department is offline
            if ($chat->auto_responder !== false) {

                $responder = $chat->auto_responder->auto_responder;

                if ($chat->status !== erLhcoreClassModelChat::STATUS_BOT_CHAT && is_object($responder) && $responder->offline_message != '' && !erLhcoreClassChat::isOnline($chat->dep_id, false, array(
                        'online_timeout' => (int) erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'],
                        'ignore_user_status' => (int)erLhcoreClassModelChatConfig::fetch('ignore_user_status')->current_value,
                        'exclude_bot' => true
                    ))) {
                    if (!isset($chatVariables['iwh_timeout']) || $chatVariables['iwh_timeout'] < time() - (int)259200) {
                        $chatVariables['iwh_timeout'] = time();
                        $chat->chat_variables_array = $chatVariables;
                        $chat->chat_variables = json_encode($chatVariables);

                        $msgResponder = new erLhcoreClassModelmsg();
                        $msgResponder->msg = trim($responder->offline_message);
                        $msgResponder->chat_id = $chat->id;
                        $msgResponder->name_support = $responder->operator != '' ? $responder->operator : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support');
                        $msgResponder->user_id = -2;
                        $msgResponder->time = time() + 5;
                        erLhcoreClassChat::getSession()->save($msgResponder);

                        $chat->last_msg_id = $msgResponder->id;

                        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.web_add_msg_admin', array(
                            'chat' => & $chat,
                            'msg' => $msgResponder
                        ));
                    }
                }
            }

            $chat->updateThis(array('update' => array(
                'pnd_time',
                'last_user_msg_time',
                'status',
                'user_id',
                'chat_variables',
                'status_sub_sub',
                'last_msg_id')));

            $eChat->utime = time();
            $eChat->updateThis();

            self::sendBotResponse($chat, $msg, array('init' => $renotify));

            // Standard event on unread chat messages
            if ($chat->has_unread_messages == 1 && $chat->last_user_msg_time < (time() - 5)) {
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.unread_chat', array(
                    'chat' => & $chat
                ));
            }

            // We dispatch same event as we were using desktop client, because it force admins and users to resync chat for new messages
            // This allows NodeJS users to know about new message. In this particular case it's admin users
            // If operator has opened chat instantly sync
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.messages_added_passive', array(
                'chat' => & $chat,
                'msg' => $msg
            ));

            // If operator has closed a chat we need force back office sync
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.nodjshelper_notify_delay', array(
                'chat' => & $chat,
                'msg' => $msg
            ));

            if ($renotify == true) {
                // General module signal that it has received an sms
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.restart_chat',array(
                    'chat' => & $chat,
                    'msg' => $msg,
                ));
            }

        } else {

            // Save chat
            $chat = new erLhcoreClassModelChat();
            $chat->nick = self::extractAttribute('nick',$conditions,$payloadMessage,'Visitor');
            $chat->phone = self::extractAttribute('phone',$conditions,$payloadMessage);
            $chat->email = self::extractAttribute('email',$conditions,$payloadMessage);
            $chat->time = time();
            $chat->pnd_time = time();
            $chat->status = 0;
            $chat->hash = erLhcoreClassChat::generateHash();
            $chat->referrer = '';
            $chat->session_referrer = '';
            $chat->dep_id = $incomingWebhook->dep_id;

            $chatVariables = array(
                'iwh_id' => $incomingWebhook->id,
            );

            $chat->chat_variables = json_encode($chatVariables);
            $chat->saveThis();

            if ($typeMessage == 'img' || $typeMessage == 'attachments') {
                if (isset($conditions['msg_'.$typeMessage.'_download']) && $conditions['msg_'.$typeMessage.'_download'] == true) {
                    $file = self::parseFiles($payloadMessage[$conditions['msg_cond_'.$typeMessage.'_body']], $chat);
                    if (!empty($file)) {
                        $payloadMessage[$conditions['msg_cond_'.$typeMessage.'_body']] = $file;
                    }
                }
            }

            // Save message
            $msg = new erLhcoreClassModelmsg();
            $msg->msg = self::extractMessageBody($msgBody, $payloadMessage);
            $msg->chat_id = $chat->id;
            $msg->user_id = 0;
            $msg->time = time();
            erLhcoreClassChat::getSession()->save($msg);

            // Save external chat
            $eChat = ($eChat instanceof erLhcoreClassModelChatIncoming) ? $eChat : (new erLhcoreClassModelChatIncoming());
            $eChat->chat_external_id = self::extractAttribute('chat_id',$conditions,$payloadMessage);

            if ($eChat->chat_external_id == '') {
                throw new Exception('ChatId attribute could not be found!');
            }

            $eChat->incoming_id = $incomingWebhook->id;
            $eChat->chat_id = $chat->id;
            $eChat->utime = time();
            $eChat->payload = json_encode($payloadAll);
            $eChat->saveThis();

            // Set bot
            erLhcoreClassChatValidator::setBot($chat, array('msg' => $msg));
            self::sendBotResponse($chat, $msg, array('init' => true));

            /**
             * Set appropriate chat attributes
             */
            $chat->last_msg_id = $msg->id;
            $chat->last_user_msg_time = $msg->time;

            // Process auto responder
            $responder = erLhAbstractModelAutoResponder::processAutoResponder($chat);

            if ($responder instanceof erLhAbstractModelAutoResponder) {
                $responderChat = new erLhAbstractModelAutoResponderChat();
                $responderChat->auto_responder_id = $responder->id;
                $responderChat->chat_id = $chat->id;
                $responderChat->wait_timeout_send = 1 - $responder->repeat_number;
                $responderChat->saveThis();

                $chat->auto_responder_id = $responderChat->id;

                if ($chat->status !== erLhcoreClassModelChat::STATUS_BOT_CHAT)
                {
                    if ($responder->offline_message != '' && !erLhcoreClassChat::isOnline($chat->dep_id, false, array(
                            'online_timeout' => (int) erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'],
                            'ignore_user_status' => (int)erLhcoreClassModelChatConfig::fetch('ignore_user_status')->current_value,
                            'exclude_bot' => true
                        ))) {
                        $msg = new erLhcoreClassModelmsg();
                        $msg->msg = trim($responder->offline_message);
                        $msg->chat_id = $chat->id;
                        $msg->name_support = $responder->operator != '' ? $responder->operator : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support');
                        $msg->user_id = -2;
                        $msg->time = time() + 5;
                        erLhcoreClassChat::getSession()->save($msg);

                        $messageResponder = $msg;

                        if ($chat->last_msg_id < $msg->id) {
                            $chat->last_msg_id = $msg->id;
                        }

                        $chatVariables['iwh_timeout'] = time();
                        $chat->chat_variables_array = $chatVariables;
                        $chat->chat_variables = json_encode($chatVariables);
                    }
                }
            }

            // Save chat
            $chat->saveThis();

            // Auto responder has something to send to visitor.
            if (isset($messageResponder)) {
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.web_add_msg_admin', array(
                    'chat' => & $chat,
                    'msg' => $messageResponder
                ));
            }

            /**
             * Execute standard callback as chat was started
             */
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chat_started', array(
                'chat' => & $chat,
                'msg' => $msg
            ));
        }
    }

    public static function extractAttribute($attr, $conditions, $payload, $defaultValue = '') {

        if (!isset($conditions[$attr]) || $conditions[$attr] == '') {
            return $defaultValue;
        } else {
            $attrParams = explode('||',$conditions[$attr]);
        }

        $valueAttribute = erLhcoreClassGenericBotActionRestapi::extractAttribute($payload, $attrParams[0], '.');

        $baseValue = $valueAttribute['found'] == true ? $valueAttribute['value'] : $defaultValue;

        if (isset($attrParams[1]) && isset($attrParams[2])) {
            $baseValueParams = explode($attrParams[1],$baseValue);
            if ($attrParams[2] == 'last') {
                $baseValue = array_pop($baseValueParams);
            } elseif (isset($baseValueParams[(int)$attrParams[2]])) {
                $baseValue = $baseValueParams[(int)$attrParams[2]];
            }
        }

        return $baseValue;
    }

    public static function sendBotResponse($chat, $msg, $params = array()) {
        if ($chat->gbot_id > 0 && (!isset($chat->chat_variables_array['gbot_disabled']) || $chat->chat_variables_array['gbot_disabled'] == 0)) {

            $chat->refreshThis();

            if (!isset($params['init']) || $params['init'] == false) {
                erLhcoreClassGenericBotWorkflow::userMessageAdded($chat, $msg);
            }

            // Find a new messages
            $botMessages = erLhcoreClassModelmsg::getList(array('filter' => array('user_id' => -2, 'chat_id' => $chat->id), 'filtergt' => array('id' => $msg->id)));
            foreach ($botMessages as $botMessage) {
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.web_add_msg_admin', array(
                    'chat' => & $chat,
                    'msg' => $botMessage
                ));
            }
        }
    }

    public static function extractMessageBody($body, $payload) {

        $matchesValues = [];

        preg_match_all('~\{\{msg\.((?:[^\{\}\}]++|(?R))*)\}\}~', $body,$matchesValues);
        $userData = [];
        if (!empty($matchesValues[0])) {
            foreach ($matchesValues[0] as $indexElement => $elementValue) {
                $valueAttribute = erLhcoreClassGenericBotActionRestapi::extractAttribute($payload, $matchesValues[1][$indexElement], '.');
                $userData[$elementValue] = $valueAttribute['found'] == true ? $valueAttribute['value'] : null;
            }
        }

        return str_replace(array_keys($userData),array_values($userData), $body);
    }
    
    public static function parseFiles($url, $chat) {
        $mediaContent = erLhcoreClassModelChatOnlineUser::executeRequest($url);
        if (!empty($mediaContent)) {

            $path = 'var/storage/'.date('Y').'y/'.date('m').'/'.date('d').'/'. $chat->id.'/';

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.uploadfile.file_path', array('path' => & $path, 'storage_id' => $chat->id));

            erLhcoreClassFileUpload::mkdirRecursive( $path );

            // File name
            $partsFilename = explode('/',$url);
            $upload_name = array_pop($partsFilename);

            // File extension
            $partsExtension = explode('.',$url);
            $file_extension = array_pop($partsExtension);

            $fileUpload = new erLhcoreClassModelChatFile();
            $fileUpload->size = strlen($mediaContent);
            $fileUpload->name = md5($url . time() . rand(0,100));
            $fileUpload->date = time();
            $fileUpload->user_id = 0;
            $fileUpload->upload_name = $upload_name;
            $fileUpload->file_path = $path;
            $fileUpload->chat_id = $chat->id;
            $fileUpload->extension = $file_extension;
            $fileUpload->type = 'application/octet-stream';
            $fileUpload->saveThis();

            // Store content
            file_put_contents($path . $fileUpload->name, $mediaContent);
            chmod($path . $fileUpload->name, 0644);

            $mimeType = erLhcoreClassThemeValidator::get_mime($path . $fileUpload->name);

            if ($mimeType !== false) {
                $mime_types = array(
                    'txt' => 'text/plain',
                    'htm' => 'text/html',
                    'html' => 'text/html',
                    'php' => 'text/html',
                    'css' => 'text/css',
                    'js' => 'application/javascript',
                    'json' => 'application/json',
                    'xml' => 'application/xml',
                    'swf' => 'application/x-shockwave-flash',
                    'flv' => 'video/x-flv',

                    // images
                    'png' => 'image/png',
                    'jpeg' => 'image/jpeg',
                    'jpg' => 'image/jpeg',
                    'gif' => 'image/gif',
                    'bmp' => 'image/bmp',
                    'ico' => 'image/vnd.microsoft.icon',
                    'tiff' => 'image/tiff',
                    'tif' => 'image/tiff',
                    'svg' => 'image/svg+xml',
                    'svgz' => 'image/svg+xml',

                    // archives
                    'zip' => 'application/zip',
                    'rar' => 'application/x-rar-compressed',
                    'exe' => 'application/x-msdownload',
                    'msi' => 'application/x-msdownload',
                    'cab' => 'application/vnd.ms-cab-compressed',

                    // audio/video
                    'mp3' => 'audio/mpeg',
                    'qt' => 'video/quicktime',
                    'mov' => 'video/quicktime',

                    // adobe
                    'pdf' => 'application/pdf',
                    'psd' => 'image/vnd.adobe.photoshop',
                    'ai' => 'application/postscript',
                    'eps' => 'application/postscript',
                    'ps' => 'application/postscript',

                    // ms office
                    'doc' => 'application/msword',
                    'rtf' => 'application/rtf',
                    'xls' => 'application/vnd.ms-excel',
                    'ppt' => 'application/vnd.ms-powerpoint',

                    // open office
                    'odt' => 'application/vnd.oasis.opendocument.text',
                    'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
                );

                $extension = array_search($mimeType,$mime_types);

                if ($extension !== false) {
                    $fileUpload->extension = $extension;
                }
            }

            $fileUpload->type = $mimeType !== false ? $mimeType : 'application/octet-stream';
            $fileUpload->saveThis();;

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.uploadfile.file_store', array('chat_file' => $fileUpload));

            return '[file='.$fileUpload->id.'_'.md5($fileUpload->name.'_'.$chat->id).']';
        }
    }

    public function sendMessage($incomingWebhook, $item) {

        $incomingChat = erLhcoreClassModelChatIncoming::findOne(array('filter' => array('chat_external_id' => $item->chat_id)));

        if (!($incomingChat instanceof erLhcoreClassModelChatIncoming)) {
            $incomingChat = new erLhcoreClassModelChatIncoming();
            $incomingChat->chat_external_id = $item->chat_id;
            $incomingChat->incoming_id = $incomingWebhook->id;
            $incomingChat->utime = time();
            $incomingChat->incoming = $incomingWebhook;
        } else {
            $incomingChat->incoming = $incomingWebhook;
            $incomingChat->incoming_id = $incomingWebhook->id;
        }


        $chat = new erLhcoreClassModelChat();
        $chat->incoming_chat = $incomingChat;
        $chat->dep_id = $item->dep_id;

        $incomingChat->chat_id = $chat->id;

        $msg = new erLhcoreClassModelmsg();
        $msg->msg = $item->message;

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.web_add_msg_admin', array('msg' => & $msg, 'chat' => & $chat));

        return $chat;
    }

}

?>