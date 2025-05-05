<?php

class erLhcoreClassLHCMobile {

    private static $disableMobile = false;

    public function perform()
    {
        $db = ezcDbInstance::get();
        $db->reconnect(); // Because it timeouts automatically, this calls to reconnect to database, this is implemented in 2.52v

        if (isset($this->args['inst_id']) && $this->args['inst_id'] > 0) {
            $cfg = \erConfigClassLhConfig::getInstance();
            $db->query('USE ' . $cfg->getSetting('db', 'database'));

            $instance = \erLhcoreClassModelInstance::fetch($this->args['inst_id']);
            \erLhcoreClassInstance::$instanceChat = $instance;

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
        } elseif ($this->args['type'] == 'subject') {
            $params = array(
                'chat' => $chat,
                'resque' => true,
                'user_id' => $this->args['user_id'],
                'init' => $this->args['init'],
                'subject_id' => $this->args['subject_id'],
            );
            self::newSubject($params);
        }
    }

    public static function sendTestNotifications($session)
    {
        $options = erLhcoreClassModelChatConfig::fetch('mobile_options')->data;

        if (isset($options['notifications']) && $options['notifications'] == true) {
            $chat = new erLhcoreClassModelChat();
            $chat->nick = 'Chat Test';

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

    public static function newSubject($params) {

        if (self::$disableMobile === true) {
            return;
        }

        if (!isset($params['resque']) && class_exists('erLhcoreClassExtensionLhcphpresque')) {
            $inst_id = class_exists('erLhcoreClassInstance') ? erLhcoreClassInstance::$instanceChat->id : 0;
            erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_mobile_notify', 'erLhcoreClassLHCMobile', array(
                'inst_id' => $inst_id,
                'type' => 'subject',
                'user_id' => (isset($params['user_id']) ? $params['user_id'] : 0),
                'init' => (isset($params['init']) ? $params['init'] : 'bot'),
                'subject_id' => (isset($params['subject_id']) ? $params['subject_id'] : 0),
                'chat_id' => $params['chat']->id));
            return;
        }

        $options = erLhcoreClassModelChatConfig::fetch('mobile_options')->data;
        if (isset($options['notifications']) && $options['notifications'] == true) {
            foreach (erLhcoreClassModelUserSession::getList(array('filternot' => array('token' => ''), 'filter' => array('error' => 0))) as $operator) {
                if (is_object($operator->user) && $operator->user->hide_online == 0 ) {

                    $subjectOperator = erLhcoreClassModelUserSetting::findOne(['filter' => ['identifier' => 'subject_id', 'user_id' => $operator->user_id]]);

                    if (!($subjectOperator instanceof erLhcoreClassModelUserSetting) || (!in_array($params['subject_id'],json_decode($subjectOperator->value,true)))) {
                        // This operator is not interested in this subject
                        continue;
                    }

                    $statusMobile = erLhcoreClassModelUserSetting::findOne(['filter' => ['identifier' => 'status_mobile', 'user_id' => $operator->user_id]]);

                    if ($statusMobile instanceof erLhcoreClassModelUserSetting) {
                        $statusMobileValue = json_decode($statusMobile->value,true);
                        if (is_array($statusMobileValue) && !empty($statusMobileValue) && !in_array($params['chat']->status,$statusMobileValue)) {
                            // Not interested in the chats of this status
                            continue;
                        }
                    }

                    $operatorName = 'system';

                    if ($params['init'] == 'op') {
                        if (isset($params['user_id']) && $params['user_id'] > 0) {

                            // This operator added subject,
                            // Do not inform anything
                            if ($params['user_id'] == $operator->user->id) {
                                continue;
                            }

                            $operatorName = erLhcoreClassModelUser::fetch($params['user_id'])->name_official;
                        }

                    } elseif ($params['chat']->gbot_id > 0) {
                        $operatorName = (string)erLhcoreClassModelGenericBotBot::fetch($params['chat']->gbot_id);
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

                    $paramsSend = array(
                        'msg' => ($params['init'] == 'op' ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/mobilenotifications','Operator') . ' "' . $operatorName . '" ' : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/mobilenotifications','Bot').' "' . $operatorName . '" ') . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/mobilenotifications','added subject').' "'.erLhAbstractModelSubject::fetch($params['subject_id']).'"',
                        'chat_type' => 'subject',
                        'title' => '"'.erLhAbstractModelSubject::fetch($params['subject_id']).'" ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/mobilenotifications','subject added'),
                    );

                    self::sendPushNotification($operator, $params['chat'], $paramsSend);

                }
            }
        } else {
            self::$disableMobile = true;
        }
    }

    public static function newMessage($params) {

        if (self::$disableMobile === true) {
            return;
        }

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
        } else {
            self::$disableMobile = true;
        }
    }

    public static function botTransfer($params) {
        if (isset($params['action']['content']['command']) && $params['action']['content']['command'] == 'stopchat' && isset($params['is_online']) && $params['is_online'] == true) {
            self::chatStarted(array('chat' => $params['chat']));
        }
    }

    public static function chatTransferred($params) {
        if (isset($params['chat']) && $params['chat'] instanceof erLhcoreClassModelChat) {
            self::chatStarted(array('chat' => $params['chat'], 'msg' => (isset($params['msg']) ? $params['msg'] : ''), 'user_id' => $params['transfer']->transfer_to_user_id));
        }
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

        if (self::$disableMobile === false) {
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
            } else {
                self::$disableMobile = true;
            }
        }

        $options = erLhcoreClassModelChatConfig::fetch('notifications_settings_op')->data;

        if (isset($options['enabled']) && $options['enabled'] == true) {
            foreach (\LiveHelperChat\Models\Notifications\OperatorSubscriber::getList(array('filter' => array('status' => 0))) as $operator) {
                if (is_object($operator->user) && (int)erLhcoreClassModelUserSetting::getSetting('hide_pers_chat',0, $operator->user_id,true,true) === 0 &&
                    (int)erLhcoreClassModelUserSetting::getSetting('new_chat_sound',1, $operator->user_id,true,true) === 1 &&
                    ($operator->user->hide_online == 0 || (int)erLhcoreClassModelUserSetting::getSetting('sn_off',0, $operator->user_id,true,true) === 1) &&
                    (
                        (isset($params['user_id']) && $params['user_id'] > 0 && $operator->user_id == $params['user_id'])
                        ||
                        (
                            (!isset($params['user_id']) || $params['user_id'] == 0) && (
                                    $operator->user_id == $params['chat']->user_id ||
                                    ($params['chat']->user_id == 0 && (int)erLhcoreClassModelUserSetting::getSetting('ownntfonly',0, $operator->user_id,true,true) === 0 )
                                )
                        )
                    )
                ) {
                    // Do not notify if user is not assigned to department
                    // Do not notify if user has only read department permission
                    if ($operator->user->all_departments == 0 && $params['chat']->user_id != $operator->user_id) {

                        $userDepartments = erLhcoreClassUserDep::getUserDepartaments($operator->user_id, $operator->user->cache_version);

                        $userReadDepartments = erLhcoreClassUserDep::getUserReadDepartments($operator->user_id, $operator->user->cache_version);

                        if (count($userDepartments) == 0) {
                            continue;
                        }

                        if (!in_array($params['chat']->dep_id,$userDepartments) || in_array($params['chat']->dep_id,$userReadDepartments)) {
                            continue;
                        }
                    }

                    $report = erLhcoreClassNotifications::sendNotificationOpChat($params['chat'], $operator /*['ignore_active' => true]*/);

                    if (!$report->isSuccess()) {
                        $operator->last_error = $report->getReason();
                        $operator->status = 1;
                        $operator->saveThis();
                    }
                }
            }
        }


    }

    public static function getAccessToken()
    {
        $presentToken = json_decode(file_get_contents('cache/token.json'), true);

        // Present token still valid
        if (isset($presentToken['accessToken']) && $presentToken['exp'] >= time() + 1 * 60) {
            return $presentToken;
            exit;
        }

        $serviceAccountContent = include 'var/external/service_account.php';

        // Load service account data
        $serviceAccount = json_decode($serviceAccountContent, true);

        $clientEmail = $serviceAccount['client_email'];
        $privateKey = $serviceAccount['private_key'];
        $firebaseScope = 'https://www.googleapis.com/auth/firebase.messaging';
        $tokenUri = 'https://oauth2.googleapis.com/token';

        // Create JWT Header
        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT'
        ];

        // Create JWT Claim Set
        $now = time();
        $exp = $now + 3600; // Token valid for 1 hour

        $claims = [
            'iss' => $clientEmail,
            'scope' => $firebaseScope,
            'aud' => $tokenUri,
            'iat' => $now,
            'exp' => $exp
        ];

        // Base64 URL Encode Function
        function base64UrlEncode($data)
        {
            return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
        }

        // Encode header and claim set
        $encodedHeader = base64UrlEncode(json_encode($header));
        $encodedClaims = base64UrlEncode(json_encode($claims));

        // Sign the JWT
        $signatureInput = $encodedHeader . '.' . $encodedClaims;
        openssl_sign($signatureInput, $signature, $privateKey, 'SHA256');

        // Encode the signature
        $encodedSignature = base64UrlEncode($signature);

        // Combine header, claims, and signature into the JWT
        $jwt = $encodedHeader . '.' . $encodedClaims . '.' . $encodedSignature;

        // Send a POST request to get the access token
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $tokenUri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl/7.29.0');

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }

        curl_close($ch);

        // Decode the response to get the access token
        $responseData = json_decode($response, true);

        if (isset($responseData['access_token'])) {
            $accessToken = $responseData['access_token'];
            $tokenData = json_encode(['accessToken' => $accessToken, 'exp' => $exp]);
            file_put_contents('cache/token.json', $tokenData);
            return ['accessToken' => $accessToken, 'exp' => $exp];
        } else {
            throw new Exception('Could not get access token!');
        }
    }

    public static function sendAndoid(erLhcoreClassModelUserSession $session, $chat, $params = array())
    {
        $options = erLhcoreClassModelChatConfig::fetch('mobile_options')->data;

        $accessToken = explode('__',$options['fcm_key']);
        $projectID = 'livehelperchat-85489';

        if (isset($options['use_local_service_file']) && $options['use_local_service_file'] == true) {
            $serviceAccount = include 'var/external/service_account.php';
            $projectID = json_decode($serviceAccount, true)['project_id'];
        }

        if (count($accessToken) != 2 || (int)$accessToken[1] < time() + 60) {
            if (isset($options['use_local_service_file']) && $options['use_local_service_file'] == true) {
                $newAccessToken = self::getAccessToken();
            } else {
                $newAccessToken = json_decode(erLhcoreClassModelChatOnlineUser::executeRequest('https://mobiletoken.livehelperchat.com/', [], ['timeout' => 7, 'connect_timeout' => 7]),true);
            }
            if (isset($newAccessToken['accessToken']) && isset($newAccessToken['exp'])) {
                $accessToken[0] = $newAccessToken['accessToken'];
                $mbOptions = erLhcoreClassModelChatConfig::fetch('mobile_options');
                $options['fcm_key'] = $newAccessToken['accessToken'] . '__' . $newAccessToken['exp'];
                $mbOptions->explain = '';
                $mbOptions->type = 0;
                $mbOptions->hidden = 1;
                $mbOptions->identifier = 'mobile_options';
                $mbOptions->value = serialize($options);
                $mbOptions->saveThis();
            } else {
                erLhcoreClassLog::write('Fetching AccessToken failed. Make sure you server can connect to https://mobiletoken.livehelperchat.com/');
                return false;
            }
        }

        // API access key from Google API's Console
        $chatSimplified = $chat->getState();

        $fields = ["message" => [
                'token' => $session->device_token,
                'notification' => array(
                    "title" => $params['title'],
                    "body" => isset($params['msg']) ? preg_replace('#\[[^\]]+\]#', '',strip_tags($params['msg'])) : preg_replace('#\[[^\]]+\]#', '', erLhcoreClassChat::getGetLastChatMessagePending($chat->id))
                ),
                'data' => array(
                    "click_action"=> "FLUTTER_NOTIFICATION_CLICK",
                    "server_id" => $session->token,
                    "m" =>  $params['title'],
                    "chat_type" => $params['chat_type'],
                    "msg" => isset($params['msg']) ? preg_replace('#\[[^\]]+\]#', '',strip_tags($params['msg'])) : preg_replace('#\[[^\]]+\]#', '', erLhcoreClassChat::getGetLastChatMessagePending($chat->id)),
                    "chat" => json_encode($chatSimplified)
                )
            ]
        ];

        $channelName = '';

        if ($params['chat_type'] == 'pending') {
            $channelName = 'com.livehelperchat.chat.channel.NEWCHAT';
        } elseif ($params['chat_type'] == 'new_msg') {
            $channelName = 'com.livehelperchat.chat.channel.NEWMESSAGE';
        } elseif ($params['chat_type'] == 'new_group_msg') {
            $channelName = 'com.livehelperchat.chat.channel.NEWGROUPMESSAGE';
        } elseif ($params['chat_type'] == 'subject') {
            $channelName = 'com.livehelperchat.chat.channel.SUBJECT';
        }

        if ($channelName != '') {
            $fields['message']['android'] = [
                "priority" => "high",
                'notification' =>  [
                    "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                    "channel_id" => $channelName,
                    "sound" => "default"
                ]
            ];
            $fields['message']['apns']['payload']['aps']['category'] = 'FLUTTER_NOTIFICATION_CLICK';
            $fields['message']['apns']['headers']['apns-priority'] = "10";
            $fields['message']['apns']['payload']['aps']['sound'] = "default";
        }


        $headers = array
        (
            'Authorization: Bearer ' . $accessToken[0],
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/'.$projectID.'/messages:send' );
        curl_setopt($ch,CURLOPT_POST, true );
        curl_setopt($ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        curl_setopt($ch,CURLOPT_TIMEOUT, 10);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,  10);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch,CURLOPT_USERAGENT, 'curl/7.29.0');

        $result = curl_exec($ch );
        curl_close( $ch );

        $data = json_decode($result,true);
        if (isset($data['error']) && $data['error']['status'] == 'NOT_FOUND') {
            $session->error = 1;
            $session->last_error = json_encode($data);
            $session->updateThis();
        } elseif (isset($data['error'])) {
            $session->last_error = json_encode($data);
            $session->updateThis();
        }

        return $data;
    }

}

?>