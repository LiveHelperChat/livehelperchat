<?php

class erLhcoreClassChatWorkflow {

    /**
     * Message for timeout
     */
    public static function timeoutWorkflow(erLhcoreClassModelChat & $chat)
    {
        $operator = $chat->auto_responder->auto_responder->operator;
        $name_support = $operator != '' ? $operator : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support');

        if (trim($chat->auto_responder->auto_responder->timeout_message) != '') {
            $msg = new erLhcoreClassModelmsg();
            $msg->msg = erLhcoreClassGenericBotWorkflow::translateMessage(trim($chat->auto_responder->auto_responder->timeout_message), array('chat' => $chat));
            $msg->chat_id = $chat->id;
            $msg->name_support = $name_support;
            $msg->user_id = -2;
            $msg->time = time();
            erLhcoreClassChat::getSession()->save($msg);

            if ($chat->last_msg_id < $msg->id) {
                $chat->last_msg_id = $msg->id;
            }

            $chat->updateThis(array('update' => array('last_msg_id')));
        }

        $chat->auto_responder->auto_responder->getMeta($chat, 'pending_op', 1, array('override_nick' => $name_support, 'store_messages' => true));
    }

    /**
     * Transfer workflow between departments
     * */
    public static function transferWorkflow(erLhcoreClassModelChat & $chat, $params = array())
    {
        $chat->transfer_if_na = 0;
        $chat->transfer_timeout_ts = time();

        if ($chat->department !== false && ($departmentTransfer = $chat->department->department_transfer) !== false) {

            $botConfiguration = $chat->department->bot_configuration_array;

            $chat->dep_id = $departmentTransfer->id;

            $msg = new erLhcoreClassModelmsg();

            if (isset($params['offline_operators']) && $params['offline_operators'] == true) {
                $msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Transferred to').' "'.$departmentTransfer.'" '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','as no operators online in').' "'.$chat->department.'"';
            } else {
                $msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Chat was automatically transferred to').' "'.$departmentTransfer.'" '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','from').' "'.$chat->department.'"';
            }

            $msg->chat_id = $chat->id;
            $msg->user_id = -1;


            $chat->last_user_msg_time = $msg->time = time();

            erLhcoreClassChat::getSession()->save($msg);

            if ($chat->last_msg_id < $msg->id) {
                $chat->last_msg_id = $msg->id;
            }

            if ($departmentTransfer->inform_unread == 1) {
                $chat->reinform_timeout = $departmentTransfer->inform_unread_delay;
                $chat->unread_messages_informed = 0;
            }

            // Our new department also has a transfer rule
            if ($departmentTransfer->department_transfer !== false) {
                $chat->transfer_if_na = 1;
                $chat->transfer_timeout_ac = $departmentTransfer->transfer_timeout;
            }

            // Reset user on chat transfer to other department if required
            if (isset($botConfiguration['ru_on_transfer']) && $botConfiguration['ru_on_transfer'] == 1 && $chat->user_id > 0) {

                if ($chat->user_id > 0) {
                    erLhcoreClassChat::updateActiveChats($chat->user_id);
                }

                $chat->user_id = 0;
            }

            if ($chat->department->nc_cb_execute == 1) {
                $chat->nc_cb_executed = 0;
            }

            erLhAbstractModelAutoResponder::updateAutoResponder($chat);

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.data_changed_assigned_department',array('chat' => & $chat, 'from_dep' => $chat->department, 'to_dep' => $departmentTransfer));

            $chat->updateThis(array('update' => array('dep_id','last_user_msg_time','last_msg_id','reinform_timeout','unread_messages_informed','user_id','na_cb_executed','transfer_if_na','transfer_timeout_ts','transfer_timeout_ac')));
        } else {
            $chat->updateThis(array('update' => array('transfer_if_na','transfer_timeout_ts')));
        }


    }

    public static function mainUnansweredChatWorkflow() {
        $output = '';
        if ( erLhcoreClassModelChatConfig::fetch('run_unaswered_chat_workflow')->current_value > 0) {

            $output .= "Starting unaswered chats workflow\n";

            $delay = time()-(erLhcoreClassModelChatConfig::fetch('run_unaswered_chat_workflow')->current_valu*60);

            foreach (erLhcoreClassChat::getList(array('limit' => 500, 'filterlt' => array('time' => $delay), 'filter' => array('status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT, 'na_cb_executed' => 0))) as $chat) {
                erLhcoreClassChatWorkflow::unansweredChatWorkflow($chat);
                $output .= "executing unanswered callback for chat - ".$chat->id."\n";
            }

            $output .= "Ended unaswered chats workflow\n";
        }

        return $output;
    }
    /*
     * Chat was unanswered for n minits, execute callback.
     * */
    public static function unansweredChatWorkflow(erLhcoreClassModelChat & $chat){

        $chat->na_cb_executed = 1;
        $chat->updateThis(array('update' => array('na_cb_executed')));

        // Execute callback if it exists
        $extensions = erConfigClassLhConfig::getInstance()->getOverrideValue( 'site', 'extensions' );
        $instance = erLhcoreClassSystem::instance();

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.unread_chat_workflow',array('chat' => & $chat));

        foreach ($extensions as $ext) {
            $callbackFile = $instance->SiteDir . '/extension/' . $ext . '/callbacks/unanswered_chat.php';
            if (file_exists($callbackFile)) {
                include $callbackFile;
            }
        }
    }

    public static function unreadInformWorkflow($options, & $chat) {

        $chat->unread_messages_informed = 1;
        $chat->updateThis(array('update' => array('unread_messages_informed')));

        if (in_array('mail', $options['options'])) {
            erLhcoreClassChatMail::sendMailUnacceptedChat($chat,7);
        }

        if (in_array('xmp', $options['options'])) {
            $errors = array();
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('xml.before_send_xmp_message', array('chat' => & $chat, 'errors' => & $errors));

            if (empty($errors)) {
                erLhcoreClassXMP::sendXMPMessage($chat);
            }
        }

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chat_unread_message',array('chat' => & $chat));

        // Execute callback if it exists
        $extensions = erConfigClassLhConfig::getInstance()->getOverrideValue( 'site', 'extensions' );
        $instance = erLhcoreClassSystem::instance();

        foreach ($extensions as $ext) {
            $callbackFile = $instance->SiteDir . '/extension/' . $ext . '/callbacks/unread_message_chat.php';
            if (file_exists($callbackFile)) {
                include $callbackFile;
            }
        }

    }

    public static function chatAcceptedWorkflow($options, & $chat) {
        if (in_array('mail_accepted', $options['options'])) {
            erLhcoreClassChatMail::sendMailUnacceptedChat($chat,9);
        }

        if (in_array('xmp_accepted', $options['options'])) {
            $errors = array();
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('xml.before_send_xmp_message', array('chat' => & $chat, 'errors' => & $errors));

            if (empty($errors)) {
                erLhcoreClassXMP::sendXMPMessage($chat, array('template' => 'xmp_accepted_message', 'recipients_setting' => 'xmp_users_accepted'));
            }
        }

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chat_accepted',array('chat' => & $chat));
    }


    public static function newChatInformWorkflow($options, & $chat) {

        $chat->nc_cb_executed = 1;
        $chat->updateThis(array('update' => array('nc_cb_executed')));

        if (in_array('mail', $options['options'])) {
            erLhcoreClassChatMail::sendMailUnacceptedChat($chat);
        }

        if (in_array('xmp', $options['options'])) {
            $errors = array();
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('xml.before_send_xmp_message', array('chat' => & $chat, 'errors' => & $errors));

            if (empty($errors)) {
                erLhcoreClassXMP::sendXMPMessage($chat);
            }
        }

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.new_chat',array('chat' => & $chat));

        // Execute callback if it exists
        $extensions = erConfigClassLhConfig::getInstance()->getOverrideValue( 'site', 'extensions' );
        $instance = erLhcoreClassSystem::instance();

        foreach ($extensions as $ext) {
            $callbackFile = $instance->SiteDir . '/extension/' . $ext . '/callbacks/new_chat.php';
            if (file_exists($callbackFile)) {
                include $callbackFile;
            }
        }
    }

    public static function automaticChatClosing() {

        $closedChatsNumber = 0;
        $timeout = (int)erLhcoreClassModelChatConfig::fetch('autoclose_timeout')->current_value;
        if ($timeout > 0) {

            // Close normal chats
            $delay = time()-($timeout*60);
            foreach (erLhcoreClassChat::getList(array('limit' => 500,'filtergt' => array('last_user_msg_time' => 0), 'filterlt' => array('last_user_msg_time' => $delay), 'filter' => array('status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT))) as $chat) {

                if ($chat->cls_us == 0) {
                    $chat->cls_us = $chat->user_status_front + 1;
                }

                $chat->status = erLhcoreClassModelChat::STATUS_CLOSED_CHAT;

                $msg = new erLhcoreClassModelmsg();
                $msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Chat was closed by cron!');
                $msg->chat_id = $chat->id;
                $msg->user_id = -1;

                $chat->last_user_msg_time = $msg->time = time();

                erLhcoreClassChat::getSession()->save($msg);

                if ($chat->last_msg_id < $msg->id) {
                    $chat->last_msg_id = $msg->id;
                }

                if ($chat->wait_time == 0) {
                    $chat->wait_time = time() - ($chat->pnd_time > 0 ? $chat->pnd_time : $chat->time);
                }

                $chat->chat_duration = erLhcoreClassChat::getChatDurationToUpdateChatID($chat);
                $chat->cls_time = time();
                $chat->has_unread_messages = 0;

                $chat->updateThis();

                erLhcoreClassChat::closeChatCallback($chat, $chat->user);

                erLhcoreClassChat::updateActiveChats($chat->user_id);

                $closedChatsNumber++;
            }

            // Close pending chats where the only message is user initial message
            foreach (erLhcoreClassChat::getList(array('limit' => 500,'filterlt' => array('time' => $delay), 'filterin' => array('status' => array(erLhcoreClassModelChat::STATUS_PENDING_CHAT, erLhcoreClassModelChat::STATUS_ACTIVE_CHAT)),'filter' => array('last_user_msg_time' => 0))) as $chat) {

                if ($chat->cls_us == 0) {
                    $chat->cls_us = $chat->user_status_front + 1;
                }

                $chat->status = erLhcoreClassModelChat::STATUS_CLOSED_CHAT;

                $msg = new erLhcoreClassModelmsg();
                $msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Chat was closed by cron!');
                $msg->chat_id = $chat->id;
                $msg->user_id = -1;

                $chat->last_user_msg_time = $msg->time = time();

                erLhcoreClassChat::getSession()->save($msg);

                if ($chat->last_msg_id < $msg->id) {
                    $chat->last_msg_id = $msg->id;
                }

                if ($chat->wait_time == 0) {
                    $chat->wait_time = time() - ($chat->pnd_time > 0 ? $chat->pnd_time : $chat->time);
                }

                $chat->chat_duration = erLhcoreClassChat::getChatDurationToUpdateChatID($chat);
                $chat->cls_time = time();
                $chat->has_unread_messages = 0;
                $chat->updateThis();

                erLhcoreClassChat::closeChatCallback($chat, $chat->user);

                erLhcoreClassChat::updateActiveChats($chat->user_id);

                $closedChatsNumber++;
            }
        }

        $timeout = (int)erLhcoreClassModelChatConfig::fetch('autoclose_timeout_pending')->current_value;
        if ($timeout > 0) {

            $delay = time()-($timeout*60);
            foreach (erLhcoreClassChat::getList(array('limit' => 500,'filterlt' => array('time' => $delay), 'filterin' => array('status' => array(erLhcoreClassModelChat::STATUS_PENDING_CHAT)))) as $chat) {
                if ($chat->cls_us == 0) {
                    $chat->cls_us = $chat->user_status_front + 1;
                }

                $chat->status = erLhcoreClassModelChat::STATUS_CLOSED_CHAT;

                $msg = new erLhcoreClassModelmsg();
                $msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Chat was closed by cron!');
                $msg->chat_id = $chat->id;
                $msg->user_id = -1;

                $chat->last_user_msg_time = $msg->time = time();

                erLhcoreClassChat::getSession()->save($msg);

                if ($chat->last_msg_id < $msg->id) {
                    $chat->last_msg_id = $msg->id;
                }

                if ($chat->wait_time == 0) {
                    $chat->wait_time = time() - ($chat->pnd_time > 0 ? $chat->pnd_time : $chat->time);
                }

                $chat->chat_duration = erLhcoreClassChat::getChatDurationToUpdateChatID($chat);
                $chat->cls_time = time();
                $chat->has_unread_messages = 0;
                $chat->updateThis();

                erLhcoreClassChat::closeChatCallback($chat, $chat->user);

                erLhcoreClassChat::updateActiveChats($chat->user_id);

                $closedChatsNumber++;
            }
        }

        $timeout = (int)erLhcoreClassModelChatConfig::fetch('autoclose_timeout_active')->current_value;
        if ($timeout > 0) {
            $delay = time()-($timeout*60);
            foreach (erLhcoreClassChat::getList(array('limit' => 500,'filterlt' => array('time' => $delay), 'filterin' => array('status' => array(erLhcoreClassModelChat::STATUS_ACTIVE_CHAT)))) as $chat) {

                if ($chat->cls_us == 0) {
                    $chat->cls_us = $chat->user_status_front + 1;
                }

                $chat->status = erLhcoreClassModelChat::STATUS_CLOSED_CHAT;

                $msg = new erLhcoreClassModelmsg();
                $msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Chat was closed by cron!');
                $msg->chat_id = $chat->id;
                $msg->user_id = -1;

                $chat->last_user_msg_time = $msg->time = time();

                erLhcoreClassChat::getSession()->save($msg);

                if ($chat->last_msg_id < $msg->id) {
                    $chat->last_msg_id = $msg->id;
                }

                if ($chat->wait_time == 0) {
                    $chat->wait_time = time() - ($chat->pnd_time > 0 ? $chat->pnd_time : $chat->time);
                }

                $chat->chat_duration = erLhcoreClassChat::getChatDurationToUpdateChatID($chat);
                $chat->cls_time = time();
                $chat->has_unread_messages = 0;
                $chat->updateThis();

                erLhcoreClassChat::closeChatCallback($chat, $chat->user);

                erLhcoreClassChat::updateActiveChats($chat->user_id);

                $closedChatsNumber++;
            }
        }

        $timeout = (int)erLhcoreClassModelChatConfig::fetch('autoclose_timeout_bot')->current_value;
        if ($timeout > 0) {
            $delay = time()-($timeout*60);
            foreach (erLhcoreClassChat::getList(array('limit' => 500,'customfilter' => array('((last_user_msg_time = 0 AND time < ' . $delay . ') OR (last_user_msg_time > 0 AND last_user_msg_time < ' . $delay . '))'), 'filterin' => array('status' => array(erLhcoreClassModelChat::STATUS_BOT_CHAT)))) as $chat) {

                if ($chat->cls_us == 0) {
                    $chat->cls_us = $chat->user_status_front + 1;
                }

                $chat->status = erLhcoreClassModelChat::STATUS_CLOSED_CHAT;

                $msg = new erLhcoreClassModelmsg();
                $msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Chat was closed by cron!');
                $msg->chat_id = $chat->id;
                $msg->user_id = -1;

                $chat->last_user_msg_time = $msg->time = time();

                erLhcoreClassChat::getSession()->save($msg);

                if ($chat->last_msg_id < $msg->id) {
                    $chat->last_msg_id = $msg->id;
                }

                if ($chat->wait_time == 0) {
                    $chat->pnd_time = time();
                    $chat->wait_time = 1;
                }

                $chat->chat_duration = erLhcoreClassChat::getChatDurationToUpdateChatID($chat);
                $chat->cls_time = time();
                $chat->has_unread_messages = 0;
                $chat->updateThis();

                erLhcoreClassChat::closeChatCallback($chat, $chat->user);

                erLhcoreClassChat::updateActiveChats($chat->user_id);

                $closedChatsNumber++;
            }
        }

        $timeout = (int)erLhcoreClassModelChatConfig::fetch('autoclose_activity_timeout')->current_value;
        if ($timeout > 0) {
            $delay = time()-($timeout*60);
            foreach (erLhcoreClassChat::getList(array('limit' => 500, 'customfilter' => array('((
            (last_user_msg_time = 0 AND last_op_msg_time = 0 AND time < ' . $delay . ') OR 
            (last_user_msg_time > 0 AND last_user_msg_time >= last_op_msg_time AND last_user_msg_time < ' . $delay . ') OR 
            (last_op_msg_time > 0 AND last_op_msg_time >= last_user_msg_time AND last_op_msg_time < ' . $delay . ') 
            ) AND (GREATEST(`pnd_time`,`time`) + `wait_time`) < '.$delay.')'), 'filterin' => array('status' => array(erLhcoreClassModelChat::STATUS_ACTIVE_CHAT)))) as $chat) {

                if ($chat->cls_us == 0) {
                    $chat->cls_us = $chat->user_status_front + 1;
                }

                $chat->status = erLhcoreClassModelChat::STATUS_CLOSED_CHAT;

                $msg = new erLhcoreClassModelmsg();
                $msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Chat was closed by cron because of inactivity!');
                $msg->chat_id = $chat->id;
                $msg->user_id = -1;

                $chat->last_user_msg_time = $msg->time = time();

                erLhcoreClassChat::getSession()->save($msg);

                if ($chat->last_msg_id < $msg->id) {
                    $chat->last_msg_id = $msg->id;
                }

                if ($chat->wait_time == 0) {
                    $chat->wait_time = time() - ($chat->pnd_time > 0 ? $chat->pnd_time : $chat->time);
                }

                $chat->chat_duration = erLhcoreClassChat::getChatDurationToUpdateChatID($chat);
                $chat->cls_time = time();
                $chat->has_unread_messages = 0;
                $chat->updateThis();

                erLhcoreClassChat::closeChatCallback($chat, $chat->user);

                erLhcoreClassChat::updateActiveChats($chat->user_id);

                $closedChatsNumber++;
            }
        }
        
        $timeoutParts = explode(',',str_replace(' ','',erLhcoreClassModelChatConfig::fetch('autoclose_abandon_pending')->current_value));

        if ((int)$timeoutParts[0] > 0) {
            $delay = time()-((int)$timeoutParts[0]*60);
            $delayMobile = time()-((isset($timeoutParts[1]) && is_numeric($timeoutParts[1]) ? (int)$timeoutParts[1] : (int)$timeoutParts[0]) * 60);
            $closeActive = isset($timeoutParts[2]) && is_numeric($timeoutParts[2]) ? (int)$timeoutParts[2] : 0;
            if ($closeActive == 1) {
                $closeTypes = array(
                    erLhcoreClassModelChat::STATUS_ACTIVE_CHAT
                );
            } elseif ($closeActive == 2) {
                $closeTypes = array(
                    erLhcoreClassModelChat::STATUS_PENDING_CHAT,
                    erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,
                );
            } elseif ($closeActive == 3) {
                $closeTypes = array(
                    erLhcoreClassModelChat::STATUS_PENDING_CHAT,
                    erLhcoreClassModelChat::STATUS_BOT_CHAT,
                );
            } elseif ($closeActive == 4) {
                $closeTypes = array(
                    erLhcoreClassModelChat::STATUS_BOT_CHAT,
                );
            } else {
                $closeTypes = array(
                    erLhcoreClassModelChat::STATUS_PENDING_CHAT
                );
            }

            $chatsToClose = erLhcoreClassChat::getList(array('limit' => 500, 'customfilter' => array('(`status_sub` IN ('.
                erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW . ',' .
                erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT . ',' .
                erLhcoreClassModelChat::STATUS_SUB_SURVEY_COMPLETED . ',' .
                erLhcoreClassModelChat::STATUS_SUB_CONTACT_FORM . ') OR (`lsync` > 0 AND ((`lsync` < '. $delay .' AND `device_type` = 0) OR  (`lsync` < '. $delayMobile .' AND `device_type` IN (1,2)))))'),
                'filterin' => array('status' => $closeTypes)));

            $avoidCloseCallback = count($chatsToClose) == 500;

            foreach ($chatsToClose as $chat) {

                if ($chat->cls_us == 0) {
                    $chat->cls_us = $chat->user_status_front + 1;
                }

                $statusOriginal = $chat->status;

                $chat->status = erLhcoreClassModelChat::STATUS_CLOSED_CHAT;

                $msg = new erLhcoreClassModelmsg();
                $msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Chat was closed by cron because visitor left the pending chat!');
                $msg->chat_id = $chat->id;
                $msg->user_id = -1;

                $chat->last_user_msg_time = $msg->time = time();

                erLhcoreClassChat::getSession()->save($msg);

                if ($chat->last_msg_id < $msg->id) {
                    $chat->last_msg_id = $msg->id;
                }

                if ($chat->wait_time == 0) {
                    if ($statusOriginal == erLhcoreClassModelChat::STATUS_BOT_CHAT) {
                        $chat->pnd_time = time();
                        $chat->wait_time = 1;
                    } else {
                        $chat->wait_time = time() - ($chat->pnd_time > 0 ? $chat->pnd_time : $chat->time);
                    }
                }

                $chat->chat_duration = erLhcoreClassChat::getChatDurationToUpdateChatID($chat);
                $chat->cls_time = time();
                $chat->has_unread_messages = 0;
                $chat->updateThis();

                if (!$avoidCloseCallback) {
                    erLhcoreClassChat::closeChatCallback($chat, $chat->user);
                }

                erLhcoreClassChat::updateActiveChats($chat->user_id);

                $closedChatsNumber++;
            }
        }


        return $closedChatsNumber;
    }

    public static function automaticChatPurge() {

        $purgedChatsNumber = 0;

        $timeout = (int)erLhcoreClassModelChatConfig::fetch('autopurge_timeout')->current_value;
        if ($timeout > 0) {
            $delay = time()-($timeout*60);
            foreach (erLhcoreClassChat::getList(array('limit' => 500,'filtergt' => array('last_user_msg_time' => 0), 'filterlt' => array('last_user_msg_time' => $delay), 'filter' => array('status' => erLhcoreClassModelChat::STATUS_CLOSED_CHAT))) as $chat) {
                $chat->removeThis();
                erLhcoreClassChat::updateActiveChats($chat->user_id);

                if ($chat->department !== false) {
                    erLhcoreClassChat::updateDepartmentStats($chat->department);
                }

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.delete', array(
                    'chat' => & $chat
                ));

                $purgedChatsNumber++;
            }
        }

        return $purgedChatsNumber;
    }

    public static function autoAssign(& $chat, $department, $params = array()) {

        if (is_object($department) && $department->active_balancing == 1 && ($department->max_ac_dep_chats == 0 || $department->active_chats_counter < $department->max_ac_dep_chats) && ($chat->user_id == 0 || ($department->max_timeout_seconds > 0 && $chat->tslasign < time()-$department->max_timeout_seconds)) ){

            $isOnlineUser = (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'];

            $db = ezcDbInstance::get();

            try {

                $botConfiguration = $department->bot_configuration_array;

                if (
                    isset($botConfiguration['auto_delay_var']) && $botConfiguration['auto_delay_var'] != '' &&
                    isset($botConfiguration['auto_delay_timeout']) && $botConfiguration['auto_delay_timeout'] > 0 &&
                    (time() - $chat->time) < $botConfiguration['auto_delay_timeout']
                )
                {
                    $valueToCompare = null;
                    if (strpos($botConfiguration['auto_delay_var'],'additional_data') !== false) {
                        $additionalDataArray = $chat->additional_data_array;

                        if (is_array($additionalDataArray)) {
                            foreach ($additionalDataArray as $additionalItem) {
                                $valueCompare = false;

                                if (isset($additionalItem['identifier'])){
                                    $valueCompare = $additionalItem['identifier'];
                                } elseif (isset($additionalItem['key'])){
                                    $valueCompare = $additionalItem['key'];
                                }

                                if ($valueCompare !== false && $valueCompare == str_replace('additional_data.','',$botConfiguration['auto_delay_var'])) {
                                    $valueToCompare = $additionalItem['value'];
                                    break;
                                }
                            }
                        }

                    } elseif (strpos($botConfiguration['auto_delay_var'],'chat_variable') !== false) {
                        $additionalDataArray = $chat->chat_variables_array;
                        if (is_array($additionalDataArray)) {
                            $variableName = str_replace('chat_variable.','', $botConfiguration['auto_delay_var']);
                            if (isset($chat->chat_variables_array[$variableName]) && $chat->chat_variables_array[$variableName] != '') {
                                $valueToCompare = $chat->chat_variables_array[$variableName];
                            }
                        }
                    } else {

                        $statusWorkflowPermit = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.workflow.autoassign_permit', array(
                            'department' => & $department,
                            'chat' => & $chat,
                            'params' => & $params,
                        ));

                        if ($statusWorkflowPermit !== false) {
                            if (isset($statusWorkflowPermit['stop']) && $statusWorkflowPermit['stop'] == true) {
                                $valueToCompare = true;
                            }
                        }
                    }

                    // Variable was not found ignore chat for a while
                    if ($valueToCompare === null) {
                        return;
                    }
                }

                $db->beginTransaction();

                // Lock chat record for update untill we finish this procedure
                erLhcoreClassChat::lockDepartment($department->id, $db);

                if ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT && ($chat->user_id == 0 || ($department->max_timeout_seconds > 0 && $chat->tslasign < time()-$department->max_timeout_seconds))) {

                    $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.workflow.autoassign', array(
                        'department' => & $department,
                        'chat' => & $chat,
                        'is_online' => & $isOnlineUser,
                        'params' => & $params,
                    ));

                    // There was no callbacks or file not found etc, we try to download from standard location
                    if ($statusWorkflow === false) {

                        $condition = '(active_chats + pending_chats)';
                        if ($department->exclude_inactive_chats == 1) {
                            $condition = '((pending_chats + active_chats) - inactive_chats)';
                        }

                        if ($department->max_active_chats > 0) {
                            $appendSQL = " AND ((max_chats = 0 AND {$condition} < :max_active_chats) OR (max_chats > 0 AND {$condition} < max_chats))";
                        } else {
                            $appendSQL = " AND ((max_chats > 0 AND {$condition} < max_chats) OR (max_chats = 0))";
                        }

                        if (!isset($params['include_ignored_users']) || $params['include_ignored_users'] == false) {
                            $appendSQL .= " AND exclude_autoasign = 0";
                        }

                        // Allow limit by provided user_ids
                        // Usefull for extension which has custom auto assign workflow
                        if (isset($params['user_ids'])) {
                            if (empty($params['user_ids'])) {
                                return array('status' => erLhcoreClassChatEventDispatcher::STOP_WORKFLOW, 'user_id' => 0);
                            }

                            $appendSQL .= ' AND `lh_userdep`.`user_id` IN (' . implode(', ',$params['user_ids']) . ')';
                        }

                        $sort = 'last_accepted ASC';
                        if (isset($botConfiguration['auto_lower_limit']) && $botConfiguration['auto_lower_limit'] == '1') {
                            $sort = 'active_chats ASC, last_accepted ASC';
                        }

                        $sql = "SELECT user_id FROM lh_userdep WHERE last_accepted < :last_accepted AND ro = 0 AND hide_online = 0 AND dep_id = :dep_id AND (`lh_userdep`.`last_activity` > :last_activity OR `lh_userdep`.`always_on` = 1) AND user_id != :user_id {$appendSQL} ORDER BY {$sort} LIMIT 1";

                        $tryDefault = true;

                        // Try to assign to operator speaking same language first
                        if ($department->assign_same_language == 1 && $chat->chat_locale != '') {

                            $sqlLanguages =  "SELECT `lh_userdep`.`user_id` FROM lh_userdep INNER JOIN lh_speech_user_language ON `lh_speech_user_language`.`user_id` = `lh_userdep`.`user_id` WHERE last_accepted < :last_accepted AND ro = 0 AND hide_online = 0 AND dep_id = :dep_id AND (`lh_userdep`.`last_activity` > :last_activity OR `lh_userdep`.`always_on` = 1) AND `lh_userdep`.`user_id` != :user_id AND `lh_speech_user_language`.`language` = :chatlanguage {$appendSQL} ORDER BY {$sort} LIMIT 1";

                            $db = ezcDbInstance::get();
                            $stmt = $db->prepare($sqlLanguages);
                            $stmt->bindValue(':dep_id',$department->id,PDO::PARAM_INT);
                            $stmt->bindValue(':last_activity',(time()-$isOnlineUser),PDO::PARAM_INT);
                            $stmt->bindValue(':user_id',$chat->user_id,PDO::PARAM_INT);
                            $stmt->bindValue(':last_accepted',(time() - $department->delay_before_assign),PDO::PARAM_INT);
                            $stmt->bindValue(':chatlanguage',$chat->chat_locale,PDO::PARAM_STR);

                            if ($department->max_active_chats > 0) {
                                $stmt->bindValue(':max_active_chats',$department->max_active_chats,PDO::PARAM_INT);
                            }

                            $stmt->execute();

                            $user_id = $stmt->fetchColumn();

                            if (is_numeric($user_id) && $user_id > 0) {
                                $tryDefault = false;
                            }
                        }



                        if ($tryDefault == true) {
                            $db = ezcDbInstance::get();
                            $stmt = $db->prepare($sql);
                            $stmt->bindValue(':dep_id',$department->id,PDO::PARAM_INT);
                            $stmt->bindValue(':last_activity',(time()-$isOnlineUser),PDO::PARAM_INT);
                            $stmt->bindValue(':user_id',$chat->user_id,PDO::PARAM_INT);
                            $stmt->bindValue(':last_accepted',(time() - $department->delay_before_assign),PDO::PARAM_INT);

                            if ($department->max_active_chats > 0) {
                                $stmt->bindValue(':max_active_chats',$department->max_active_chats,PDO::PARAM_INT);
                            }

                            $stmt->execute();

                            $user_id = $stmt->fetchColumn();
                        }

                    } else {
                        $db = ezcDbInstance::get();
                        $user_id = $statusWorkflow['user_id'];
                    }

                    if ($user_id > 0) {

                        $previousMessage = '';

                        $msg = new erLhcoreClassModelmsg();
                        $msg->chat_id = $chat->id;
                        $msg->user_id = -1;
                        $msg->time = time();

                        // Update previously assigned operator statistic
                        if ($chat->user_id > 0) {
                            $userOld = erLhcoreClassModelUser::fetch($chat->user_id);
                            $msg->name_support = $userOld->name_support;

                            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_msg_user_saved', array('msg' => & $msg, 'chat' => & $chat, 'user_id' => $chat->user_id));

                            $previousMessage = '[' . $chat->user_id . '] ' . $msg->name_support . ' '.  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','did not accepted chat in time.') . ' ';
                            erLhcoreClassChat::updateActiveChats($chat->user_id);
                        }

                        $userNew = erLhcoreClassModelUser::fetch($user_id);
                        $msg->name_support = $userNew->name_support;

                        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_msg_user_saved', array('msg' => & $msg, 'chat' => & $chat, 'user_id' => $user_id));

                        $msg->msg = $previousMessage . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat was assigned to') . ' [' . $userNew->id .'] ' . $msg->name_support;

                        erLhcoreClassChat::getSession()->save($msg);

                        // Set owner to a new user
                        $chat->tslasign = time();
                        $chat->user_id = $user_id;
                        $chat->last_msg_id = $msg->id;
                        $chat->updateThis(array('update' => array('last_msg_id','tslasign','user_id')));

                        erLhcoreClassUserDep::updateLastAcceptedByUser($user_id, time());

                        // Update fresh user statistic
                        erLhcoreClassChat::updateActiveChats($chat->user_id);
                    }
                }

                if (isset($user_id) && $user_id > 0) {
                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.data_changed_auto_assign',array('chat' => & $chat));
                }

                $db->commit();

            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        }
    }

    public static function presendCannedMsg($chat) {

        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.workflow.presend_canned_msg', array(
            'chat' => & $chat,
        ));

        if ($statusWorkflow === false) {
            $session = erLhcoreClassChat::getSession();
            $q = $session->createFindQuery( 'erLhcoreClassModelCannedMsg' );
            $q->where(
                $q->expr->lOr(
                    $q->expr->eq( 'department_id', $q->bindValue($chat->dep_id) ),
                    $q->expr->lAnd($q->expr->eq( 'department_id', $q->bindValue( 0 ) ),$q->expr->eq( 'user_id', $q->bindValue( 0 ) )),
                    $q->expr->eq( 'user_id', $q->bindValue($chat->user_id) )
                ),
                $q->expr->eq( 'auto_send', $q->bindValue(1) )
            );

            $q->limit(1, 0);
            $q->orderBy('user_id DESC, position ASC, id ASC' ); // Questions with matched URL has higher priority
            $items = $session->find( $q );
        } else {
            $items = $statusWorkflow['items'];
        }

        if (!empty($items)){
            $cannedMsg = array_shift($items);

            $replaceArray = array(
                '{nick}' => $chat->nick,
                '{email}' => $chat->email,
                '{phone}' => $chat->phone,
                '{operator}' => (string)$chat->user->name_support
            );

            $additionalData = $chat->additional_data_array;

            if (is_array($additionalData)) {
                foreach ($additionalData as $row) {
                    if (isset($row['identifier']) && $row['identifier'] != '') {
                        $replaceArray['{'.$row['identifier'].'}'] = $row['value'];
                    }
                }
            }

            $items = array($cannedMsg);

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.workflow.canned_message_replace',array('items' => & $items, 'user' => $chat->user, 'chat' => $chat, 'replace_array' => & $replaceArray));

            $cannedMsg = $items[0];

            // Set proper message by language
            $cannedMsg->setMessageByChatLocale($chat->chat_locale);

            $cannedMsg->setReplaceData($replaceArray);

            $msg = new erLhcoreClassModelmsg();
            $msg->msg = $cannedMsg->msg_to_user;

            if (strpos($msg->msg,'[html]') !== false && !erLhcoreClassUser::instance()->hasAccessTo('lhchat','htmlbbcodeenabled')) {
                $msg->msg = '[html] is disabled for you!';
                $msg->user_id = -1;
            }

            $msg->chat_id = $chat->id;
            $msg->user_id = $chat->user_id;
            $msg->name_support = $chat->user->name_support;

            $chat->last_op_msg_time = $chat->last_user_msg_time = $msg->time = time();
            $chat->has_unread_op_messages = 1;
            $chat->unread_op_messages_informed = 0;

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.workflow.canned_message_before_save',array('msg' => & $msg, 'chat' => & $chat));

            erLhcoreClassChat::getSession()->save($msg);

            if ($chat->last_msg_id < $msg->id) {
                $chat->last_msg_id = $msg->id;
            }

            $chat->updateThis(array('update' => array(
                'last_op_msg_time',
                'last_user_msg_time',
                'has_unread_op_messages',
                'unread_op_messages_informed',
                'last_msg_id'
            )));
        }
    }

    public static function autoInformVisitor($minutesTimeout)
    {
        if ($minutesTimeout > 0) {
            $items = erLhcoreClassChat::getList(array('limit' => 10, 'filterlt' => array('last_op_msg_time' => (time() - (1*60))), 'filter' => array('has_unread_op_messages' => 1, 'unread_op_messages_informed' => 0)));

            // Update chats instantly
            foreach ($items as $item) {
                $item->has_unread_op_messages = 0;
                $item->unread_op_messages_informed = 1;
                $item->updateThis(array('update' => array('has_unread_op_messages','unread_op_messages_informed')));
            }

            // Now inform visitors
            foreach ($items as $item) {
                erLhcoreClassChatMail::informVisitorUnreadMessage($item);
            }
        }
    }

    public static function getChatHistory($chat, $lastMessageId)
    {
        $messages = erLhcoreClassChat::getChatMessages($chat->id, erLhcoreClassChat::$limitMessages, $lastMessageId);

        $messageId = 0;
        $hasMessages = true;
        if (count($messages) == erLhcoreClassChat::$limitMessages) {
            reset($messages);
            $message = current($messages);
            $messageId = $message['id'];
        } else {
            $hasMessages = false;

            $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.workflow.get_chat_history', array(
                'chat' => $chat,
                'last_message_id' => $lastMessageId,
            ));

            if ($statusWorkflow === false) {
                if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','prev_chats') && ($online_user = $chat->online_user) !== false) {
                    $chatHistory = erLhcoreClassModelChat::findOne(array('sort' => 'id DESC','filterlt' => array('id' => $chat->id), 'filter' => array('online_user_id' => $online_user->id)));
                    if ($chatHistory instanceof erLhcoreClassModelChat) {
                        $chat = $chatHistory;
                        $hasMessages = true;
                    }
                }
            } else {
                $hasMessages = $statusWorkflow['has_messages'];
                $chat = $statusWorkflow['chat'];
            }
        }

        return array(
            'chat_id' => (is_object($chat) ? $chat->id : null),
            'chat' => (is_object($chat) ? $chat : null),
            'message_id' => $messageId,
            'messages' => $messages,
            'has_messages' => $hasMessages
        );
    }

    public static function hasPreviousChats($params)
    {
        reset($params['messages']); $firstMessage = current($params['messages']);
        $chatHistory = $params['chat'];
        $hasMessages = erLhcoreClassChat::$limitMessages == count($params['messages']);
        $firstMessageId = $firstMessage['id'];

        $params['has_messages'] = $hasMessages;
        $statusWorkflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.workflow.has_previous_messages', $params);

        if ($statusWorkflow === false) {
            if ($hasMessages == false && erLhcoreClassUser::instance()->hasAccessTo('lhchat','prev_chats') && ($online_user = $params['chat']->online_user) !== false) {
                $chatHistory = erLhcoreClassModelChat::findOne(array('sort' => 'id DESC','filterlt' => array('id' => $params['chat']->id), 'filter' => array('online_user_id' => $online_user->id)));
                if ($chatHistory instanceof erLhcoreClassModelChat) {
                    $hasMessages = true;
                    $firstMessageId = 0;
                }
            }
            return array(
                'has_messages' => $hasMessages,
                'chat_history' => $chatHistory,
                'message_id' => $firstMessageId
            );
        } else {
            return $statusWorkflow;
        }
    }
}

?>
