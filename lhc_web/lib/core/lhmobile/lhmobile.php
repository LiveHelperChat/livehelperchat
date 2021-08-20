<?php

class erLhcoreClassLHCMobile {

    public function perform()
    {
        $db = ezcDbInstance::get();
        $db->reconnect(); // Because it timeouts automatically, this calls to reconnect to database, this is implemented in 2.52v

        if (isset($this->args['inst_id']) && $this->args['inst_id'] > 0) {
            $cfg = erConfigClassLhConfig::getInstance();
            $db->query('USE ' . $cfg->getSetting('db', 'database_user_prefix') . $this->args['inst_id']);
        }

        $chatId = $this->args['chat_id'];

        $chat = erLhcoreClassModelChat::fetch($chatId);
        if (!($chat instanceof erLhcoreClassModelChat)) {
            return;
        }

        if ($this->args['type'] == 'message') {
            if ($this->args['msg_id'] > 0) {
                $msg = erLhcoreClassModelmsg::fetch($this->args['msg_id']);
                if ($msg instanceof erLhcoreClassModelmsg) {
                    self::newMessage(array(
                        'resque' => true,
                        'chat' => $chat,
                        'msg' => $msg
                    ));
                }
            }
        } elseif ($this->args['type'] == 'started') {
            $params = array('chat' => $chat, 'resque' => true);

            if ($this->args['msg_id'] > 0) {
                $msg = erLhcoreClassModelmsg::fetch($this->args['msg_id']);
                if ($msg instanceof erLhcoreClassModelmsg) {
                    $params['msg'] = $msg;
                } else {
                    // Message is gone for some reason
                    return;
                }
            }

            self::chatStarted($params);
        }
    }

    public static function sendTestNotifications($session)
    {
        $options = erLhcoreClassModelChatConfig::fetch('mobile_options')->data;

        if (isset($options['notifications']) && $options['notifications'] == true) {
            $chat = new erLhcoreClassModelChat();
            $chat->nick = 'Live Helper Chat';

            self::sendPushNotification($session, $chat, ['chat_type' => 'test_notification', 'title' => $chat->nick, 'msg' => 'Test notifications']);
        } else {
            throw new Exception('Notifications not enabled!');
        }
    }

    public static function sendPushNotification(erLhcoreClassModelUserSession $session, $chat, $params = array())
    {
        $paramsSend = $params;

        if (!isset($paramsSend['chat_type'])) {
            $paramsSend['chat_type'] = 'pending';
        }

        // We use firebase in all cases to send a notification
        if ($session->device_type == erLhcoreClassModelUserSession::DEVICE_TYPE_ANDROID || $session->device_type == erLhcoreClassModelUserSession::DEVICE_TYPE_IOS) {
            return self::sendAndoid($session, $chat, $paramsSend);
        }
    }

    public static function newGroupMessage($params) {
        $options = erLhcoreClassModelChatConfig::fetch('mobile_options')->data;
        if (isset($options['notifications']) && $options['notifications'] == true) {

            $members = erLhcoreClassModelGroupChatMember::getList(array('limit' => false, 'filter' => array('group_id' => $params['chat']->id)));

            $membersUserIds = array();
            foreach ($members as $member) {
                $membersUserIds[] = $member->user_id;
            }

            if (empty($membersUserIds)) {
                return;
            }

            foreach (erLhcoreClassModelUserSession::getList(array('filterin' => array('user_id' => $membersUserIds),'filternot' => array('user_id' => $params['msg']->user_id, 'token' => ''),'filter' => array('error' => 0))) as $operator) {
                if (is_object($operator->user)) {

                    //Set custom attributes used for mobile app
                    $params['chat']->user_id = $params['msg']->user_id;
                    $params['chat']->chat_id = $params['msg']->chat_id;
                    $params['chat']->name_official = $operator->user->name_official;

                    self::sendPushNotification($operator, $params['chat'], array(
                        'msg' => trim(erLhcoreClassBBCodePlain::make_clickable($params['msg']->msg, array('sender' => 0))),
                        'chat_type' => 'new_group_msg',
                        'title' => $params['chat']->name . ' - ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/mobilenotifications','New group message'),
                    ));
                }
            }
        }
    }

    public static function newMessage($params) {

        // Messages notifications should be send only to active chats
        // We are not interested in pending or bot chats etc.
        if ($params['chat']->status != erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) {
            return;
        }

        if (!isset($params['resque']) && class_exists('erLhcoreClassExtensionLhcphpresque')) {
            $inst_id = class_exists('erLhcoreClassInstance') ? erLhcoreClassInstance::$instanceChat->id : 0;
            erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_mobile_notify', 'erLhcoreClassLHCMobile', array('inst_id' => $inst_id, 'type' => 'message', 'msg_id' => (isset($params['msg']) ? $params['msg']->id : 0), 'chat_id' => $params['chat']->id));
            return;
        }

        $options = erLhcoreClassModelChatConfig::fetch('mobile_options')->data;
        
        if (isset($options['notifications']) && $options['notifications'] == true) {
            foreach (erLhcoreClassModelUserSession::getList(array('filternot' => array('token' => ''),'filter' => array('error' => 0))) as $operator) {
                if (is_object($operator->user) && $operator->user->hide_online == 0 && ($operator->user->id == $params['chat']->user_id || $params['chat']->user_id == 0)) {

                    $messageSoundEnabled = (int)erLhcoreClassModelUserSetting::getSetting('chat_message',1, $operator->user->id);

                    if ($messageSoundEnabled == 0) {
                        continue;
                    }

                    // Do not notify if user is not assigned to department
                    // Do not notify if user has only read department permission
                    if ($operator->user->all_departments == 0 && $params['chat']->user_id != $operator->user->id) {

                        $userDepartments = erLhcoreClassUserDep::getUserDepartaments($operator->user->id, $operator->user->cache_version);

                        $userReadDepartments = erLhcoreClassUserDep::getUserReadDepartments($operator->user->id, $operator->user->cache_version);

                        if (count($userDepartments) == 0) {
                            continue;
                        }

                        if (!in_array($params['chat']->dep_id,$userDepartments) || in_array($params['chat']->dep_id,$userReadDepartments)) {
                            continue;
                        }
                    }

                    self::sendPushNotification($operator, $params['chat'], array(
                        'msg' => $params['chat']->nick . ' - ' . trim(erLhcoreClassBBCodePlain::make_clickable($params['msg']->msg, array('sender' => 0))),
                        'chat_type' => 'new_msg',
                        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/mobilenotifications','New message'),
                    ));
                }
            }
        }
    }

    public static function botTransfer($params) {
        if (isset($params['action']['content']['command']) && $params['action']['content']['command'] == 'stopchat' && isset($params['is_online']) && $params['is_online'] == true) {
            self::chatStarted(array('chat' => $params['chat']));
        }
    }

    public static function chatTransferred($params) {
            self::chatStarted(array('chat' => $params['chat'], 'msg' => $params['msg'], 'user_id' => $params['transfer']->transfer_to_user_id));
    }

    public static function chatStarted($params) {

        // New chat notification should be send only if chat is pending
        // We are not interested in pending or bot chats etc.
        // But we are interested in direct notifications about chat
        if ($params['chat']->status != erLhcoreClassModelChat::STATUS_PENDING_CHAT && (!isset($params['user_id']) || $params['user_id'] == 0)) {
            return;
        }

        if (!isset($params['resque']) && class_exists('erLhcoreClassExtensionLhcphpresque')) {
            $inst_id = class_exists('erLhcoreClassInstance') ? erLhcoreClassInstance::$instanceChat->id : 0;
            erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_mobile_notify', 'erLhcoreClassLHCMobile', array(
                'inst_id' => $inst_id,
                'type' => 'started',
                'user_id' => (isset($params['user_id']) ? $params['user_id'] : 0),
                'msg_id' => (isset($params['msg']) && is_object($params['msg']) ? $params['msg']->id : 0),
                'chat_id' => $params['chat']->id));
            return;
        }

        $options = erLhcoreClassModelChatConfig::fetch('mobile_options')->data;
        if (isset($options['notifications']) && $options['notifications'] == true) {

            foreach (erLhcoreClassModelUserSession::getList(array('filternot' => array('token' => ''), 'filter' => array('error' => 0))) as $operator) {
                if (is_object($operator->user) && $operator->user->hide_online == 0 &&
                    (
                        (isset($params['user_id']) && $params['user_id'] > 0 && $operator->user->id == $params['user_id'])
                        ||
                        ((!isset($params['user_id']) || $params['user_id'] == 0) && ($operator->user->id == $params['chat']->user_id || $params['chat']->user_id == 0))
                    )
                ) {

                    // Do not notify if user is not assigned to department
                    // Do not notify if user has only read department permission
                    if ($operator->user->all_departments == 0 && $params['chat']->user_id != $operator->user->id) {

                        $userDepartments = erLhcoreClassUserDep::getUserDepartaments($operator->user->id, $operator->user->cache_version);

                        $userReadDepartments = erLhcoreClassUserDep::getUserReadDepartments($operator->user->id, $operator->user->cache_version);

                        if (count($userDepartments) == 0) {
                            continue;
                        }

                        if (!in_array($params['chat']->dep_id,$userDepartments) || in_array($params['chat']->dep_id,$userReadDepartments)) {
                            continue;
                        }
                    }

                    $visitor = array();
                    $visitor[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/mobilenotifications','Department').': ' . ((string)$params['chat']->department) .',  ID: ' . $params['chat']->id .', '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/mobilenotifications','Nick').': ' . $params['chat']->nick;

                    if (isset($params['msg']) && is_object($params['msg'])) {
                        if (isset($params['user_id']) && $params['user_id'] > 0) {
                            $visitor = array();
                            $visitor[] = trim(erLhcoreClassBBCodePlain::make_clickable($params['msg']->msg, array('sender' => 0))) . '';
                        } else {
                            $visitor[] = 'Message: ' . trim(erLhcoreClassBBCodePlain::make_clickable($params['msg']->msg, array('sender' => 0))) . '';
                        }
                    } elseif ($params['chat']->user_id > 0) {
                        $visitor[] = 'Chat was assigned to you';
                    }

                    self::sendPushNotification($operator, $params['chat'], array(
                        'msg' => implode("\n", $visitor),
                        'chat_type' => 'pending',
                        'title' => isset($params['user_id']) && $params['user_id'] > 0 ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/mobilenotifications','Transferred chat') : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/mobilenotifications','New chat'),
                    ));
                }
            }
        }
    }

    public static function sendIOS(erLhcoreClassModelUserSession $session, erLhcoreClassModelChat $chat, $params = array())
    {
        // Temporary disable
        // return;

        // Put your device token here (without spaces):
        $deviceToken = $session->device_token; //'38533403ad5b7f7cde88859adce2e102d1f9a41269d3858be1a290f218697079';

        // Put your private key's passphrase here:
        $passphrase = '<change_me>';

        // Put your alert message here:
        $message = isset($params['msg']) ? preg_replace('#\[[^\]]+\]#', '',strip_tags($params['msg'])) : preg_replace('#\[[^\]]+\]#', '', erLhcoreClassChat::getGetLastChatMessagePending($chat->id));

        ////////////////////////////////////////////////////////////////////////////////

        $ctx = stream_context_create();

        stream_context_set_option($ctx, 'ssl', 'local_cert', '<change_me>');


        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
        stream_context_set_option($ctx,'ssl','verify_peer',false);

        // Open a connection to the APNS server
        $fp = stream_socket_client(
            'ssl://gateway.push.apple.com:2195', $err,
            //'ssl://gateway.sandbox.push.apple.com:2195', $err,
            $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

        stream_set_blocking ($fp, 0);

        if (!$fp) {
            return;
            exit("Failed to connect: $err $errstr" . PHP_EOL);
        }

        // Create the payload body
        $body['aps'] = array(
            'alert' => $message,
            'sound' => 'new_message.wav',
            'chattype'  => $params['type'],
            'messageid'    => $chat->id . (isset($params['append_url']) ? $params['append_url'] : ''),
            'msghash'    => $chat->hash,
            'title'		=> $chat->nick,
            'badge'=> 1,
            'category' => 'ACTIONABLE',
        );

        // Encode the payload as JSON
        $payload = json_encode($body);

        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));


        if ($session->last_checked < time()-24*3600) {
            usleep(300000);

            /* Status code	Description
            0	No errors encountered
            1	Processing error
            2	Missing device token
            3	Missing topic
            4	Missing payload
            5	Invalid token size
            6	Invalid topic size
            7	Invalid payload size
            8	Invalid token
            10	Shutdown
            255	None (unknown) */

            $apple_error_response = fread($fp, 6);
            //NOTE: Make sure you set stream_set_blocking($fp, 0) or else fread will pause your script and wait forever when there is no response to be sent.

            $error_response = false;

            if ($apple_error_response) {
                $error_response = unpack('Ccommand/Cstatus_code/Nidentifier', $apple_error_response);
            }

            if (is_array($error_response) && $error_response !== false) {
                $session->removeThis();
            } else {
                $session->last_checked = time();
                $session->saveThis();
            }
        }

        fclose($fp);
    }

    public static function sendAndoid(erLhcoreClassModelUserSession $session, $chat, $params = array())
    {
        $options = erLhcoreClassModelChatConfig::fetch('mobile_options')->data;

        if (!isset($options['fcm_key']) || $options['fcm_key'] == '') {
            throw new Exception('FCM Key is not set');
        }

        // API access key from Google API's Console
        $registrationIds = array( $session->device_token );

        $chatSimplified = $chat->getState();

        $fields = array
        (
            'registration_ids' 	=> $registrationIds,
            'notification'=>array(
                "title" => $params['title'],
                "sound" => "default",
                "body" => isset($params['msg']) ? preg_replace('#\[[^\]]+\]#', '',strip_tags($params['msg'])) : preg_replace('#\[[^\]]+\]#', '', erLhcoreClassChat::getGetLastChatMessagePending($chat->id))
            ),
            'data' => array(
                "click_action"=> "FLUTTER_NOTIFICATION_CLICK",
                "server_id" => $session->token,
                "m" =>  $params['title'],
                "chat_type" => $params['chat_type'],
                "msg" => isset($params['msg']) ? preg_replace('#\[[^\]]+\]#', '',strip_tags($params['msg'])) : preg_replace('#\[[^\]]+\]#', '', erLhcoreClassChat::getGetLastChatMessagePending($chat->id)),
                "chat" => json_encode($chatSimplified)
            ),
            "priority"=>"high"
        );

        $headers = array
        (
            'Authorization: key=' . $options['fcm_key'],
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );

        $data = json_decode($result,true);
        if ($data['failure'] == 1) {
            foreach ($data['results'] as $item) {
                if (isset($item['error']) && ($item['error'] == 'NotRegistered' || $item['error'] == 'InvalidRegistration')) {
                    $session->error = 1;
                    $session->last_error = json_encode($data['results']);
                    $session->updateThis();
                }
            }
        }

        return $data;
    }

}

?>