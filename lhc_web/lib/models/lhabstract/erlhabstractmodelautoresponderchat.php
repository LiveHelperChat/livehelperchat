<?php

class erLhAbstractModelAutoResponderChat
{

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_abstract_auto_responder_chat';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'chat_id' => $this->chat_id,
            'auto_responder_id' => $this->auto_responder_id,
            'wait_timeout_send' => $this->wait_timeout_send,
            'pending_send_status' => $this->pending_send_status,
            'active_send_status' => $this->active_send_status
        ); // Which of pending status message was send the last one

        return $stateArray;
    }

    public function __toString()
    {
        return (string)$this->chat_id;
    }

    /*
     * Chat closing auto responder
     * */
    public function processClose()
    {
        if ($this->auto_responder !== false) {

            if ($this->auto_responder->close_message != '') {

                $msg = new erLhcoreClassModelmsg();
                $msg->msg = trim($this->auto_responder->close_message);
                $msg->chat_id = $this->chat->id;
                $msg->name_support = $this->chat->user !== false ? $this->chat->user->name_support : ($this->auto_responder->operator != '' ? $this->auto_responder->operator : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Live Support'));
                $msg->user_id = $this->chat->user_id > 0 ? $this->chat->user_id : - 2;
                $msg->time = time();
                erLhcoreClassChat::getSession()->save($msg);

                $this->chat->last_msg_id = $msg->id;
                $this->chat->updateThis(array('update' => array('last_msg_id')));
            }
        }
    }

    public function processAccept() {

        if ($this->auto_responder !== false && $this->auto_responder->multilanguage_message != '' && $this->chat->user_id > 0) {
            $localeShort = explode('-',$this->chat->chat_locale)[0];
            $chatLanguages = [$this->chat->chat_locale,$localeShort];

            $languagesIgnore = $this->auto_responder->languages_ignore;

            if ((empty($languagesIgnore) || empty(array_intersect($chatLanguages,$languagesIgnore))) && erLhcoreClassModelSpeechUserLanguage::getCount(array('filterlor' => array('language' => $chatLanguages),'filter' => array('user_id' => $this->chat->user_id))) > 0) {

                $msg = new erLhcoreClassModelmsg();
                $msg->msg = trim($this->auto_responder->multilanguage_message);
                $msg->chat_id = $this->chat->id;
                $msg->name_support = $this->chat->user !== false ? $this->chat->user->name_support : ($this->auto_responder->operator != '' ? $this->auto_responder->operator : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Live Support'));
                $msg->user_id = $this->chat->user_id > 0 ? $this->chat->user_id : - 2;
                $msg->time = time();
                erLhcoreClassChat::getSession()->save($msg);

                $this->chat->last_msg_id = $msg->id;
                $this->chat->updateThis(array('update' => array('last_msg_id')));
            }
        }
    }

    public function process()
    {
        if ($this->auto_responder !== false) {

            if ($this->chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT) {

                if ($this->auto_responder->ignore_pa_chat == 0 || ($this->auto_responder->ignore_pa_chat == 1 && $this->chat->user_id == 0)) { // Do not send messages to assigned pending chats

                    if ($this->wait_timeout_send <= 0 && $this->auto_responder->wait_timeout > 0 && (time() - ($this->chat->last_op_msg_time > 0 ? $this->chat->last_op_msg_time : ($this->chat->pnd_time > 0 ? $this->chat->pnd_time : $this->chat->time))) > ($this->auto_responder->wait_timeout * ($this->auto_responder->repeat_number - (abs($this->wait_timeout_send))))) {

                        $errors = array();
                        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_auto_responder_triggered', array(
                            'chat' => & $this->chat,
                            'errors' => & $errors
                        ));

                        if (empty($errors)) {
                            erLhcoreClassChatWorkflow::timeoutWorkflow($this->chat);

                            $this->wait_timeout_send ++;

                            // It was the last time this message was executed.
                            // Now we can process next one pending messages if there are any
                            if ($this->wait_timeout_send == 1) {
                                $this->pending_send_status = 1;
                            }

                            $this->saveThis();
                        } else {
                            $msg = new erLhcoreClassModelmsg();
                            $msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat', 'Auto responder got error') . ': ' . implode('; ', $errors);
                            $msg->chat_id = $this->chat->id;
                            $msg->user_id = - 1;
                            $msg->time = time();

                            if ($this->chat->last_msg_id < $msg->id) {
                                $this->chat->last_msg_id = $msg->id;
                            }

                            erLhcoreClassChat::getSession()->save($msg);
                        }
                    } elseif ($this->pending_send_status >= 1 && $this->pending_send_status < 5) {
                        for ($i = 5; $i >= 2; $i --) {
                            if ($this->pending_send_status < $i && $this->auto_responder->{'wait_timeout_' . $i} > 0 && $this->auto_responder->{'wait_timeout_' . $i} < (time() - ($this->chat->last_op_msg_time > 0 ? $this->chat->last_op_msg_time : ($this->chat->pnd_time > 0 ? $this->chat->pnd_time : $this->chat->time)))) {

                                $this->pending_send_status = $i;
                                $this->saveThis();

                                $metaMessage = $this->auto_responder->getMeta($this->chat, 'pending_op', $i, array('include_message' => true));

                                $msg = new erLhcoreClassModelmsg();
                                $msg->msg = trim($this->auto_responder->{'timeout_message_' . $i}) . $metaMessage['msg'];
                                $msg->meta_msg = $metaMessage['meta_msg'];
                                $msg->chat_id = $this->chat->id;
                                $msg->name_support = $this->auto_responder->operator != '' ? $this->auto_responder->operator : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Live Support');
                                $msg->user_id = - 2;
                                $msg->time = time();

                                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_auto_responder_msg_saved', array('msg' => & $msg, 'chat' => & $this->chat));

                                erLhcoreClassChat::getSession()->save($msg);

                                $this->chat->last_msg_id = $msg->id;
                                $this->chat->updateThis(array('update' => array('last_msg_id')));
                            }
                        }
                    }
                }

            } elseif ($this->chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) {

                $botConfiguration = $this->auto_responder->bot_configuration_array;

                // Do not reset auto responder if visitor was redirected to survey
                if (
                    !(isset($botConfiguration['dreset_survey']) && $botConfiguration['dreset_survey'] == 1 && $this->chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW) &&
                    (isset($botConfiguration['mint_reset']) && $botConfiguration['mint_reset'] > 0) &&
                    (isset($botConfiguration['maxt_reset']) && $botConfiguration['maxt_reset'] > 0))
                {
                    if ( (time() - $this->chat->lsync > $botConfiguration['mint_reset']) &&
                         (time() - $this->chat->lsync < $botConfiguration['maxt_reset']) &&
                         in_array($this->chat->device_type,array(1,2)) &&
                         $this->active_send_status < $this->auto_responder->wait_timeout_reply_total
                    ) {
                        $lsync = $this->chat->lsync;
                        $diff = time() - $lsync;
                        $this->chat->lsync = time();
                        $this->chat->last_op_msg_time = time() - $this->auto_responder->{'wait_timeout_reply_' . ($this->active_send_status - 1)}-20;
                        $this->chat->last_user_msg_time = $this->chat->last_op_msg_time - 1;

                        if ($this->chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW) {
                            $this->chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_DEFAULT;
                        }

                        $msg = new erLhcoreClassModelmsg();

                        if ($lsync > 0) {
                            $msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closechatadmin', 'Visitor auto responder was reset because of sync timeout, returned after') . ' ' . $diff . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closechatadmin', 'seconds!');
                        } else {
                            $msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closechatadmin', 'Visitor auto responder was reset because the visitor returned!');
                        }

                        $msg->chat_id = $this->chat->id;
                        $msg->user_id = - 1;
                        $msg->time = time();

                        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_auto_responder_msg_saved', array('msg' => & $msg, 'chat' => & $this->chat));

                        erLhcoreClassChat::getSession()->save($msg);

                        $this->chat->last_msg_id = $msg->id;
                        $this->chat->updateThis(array('update' => array('last_msg_id','status_sub','last_user_msg_time','last_op_msg_time','lsync','last_user_msg_time')));
                    }
                }

                if ($this->chat->status_sub != erLhcoreClassModelChat::STATUS_SUB_ON_HOLD) {
                    if (($this->chat->last_op_msg_time > $this->chat->last_user_msg_time && $this->chat->last_user_msg_time > 0) ||
                        ($this->chat->last_op_msg_time > $this->chat->time && $this->chat->last_user_msg_time == 0))
                    {
                        if ($this->chat->status_sub != erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW && $this->auto_responder->survey_timeout > 0 && (time() - $this->chat->last_op_msg_time > $this->auto_responder->survey_timeout)) {
                            $msg = new erLhcoreClassModelmsg();
                            $msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closechatadmin', 'Visitor was redirected to survey by auto responder!');
                            $msg->chat_id = $this->chat->id;
                            $msg->user_id = - 1;
                            $msg->time = time();

                            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_auto_responder_msg_saved', array('msg' => & $msg, 'chat' => & $this->chat));

                            erLhcoreClassChat::getSession()->save($msg);

                            $this->chat->last_msg_id = $msg->id;
                            $this->chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW;
                            $this->chat->updateThis(array('update' => array('last_msg_id','status_sub')));

                            if ($this->chat->user_id > 0) {
                                erLhcoreClassChat::updateActiveChats($this->chat->user_id);
                            }

                            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.redirected_to_survey_by_autoresponder',array('chat' => & $this->chat));

                            // Survey redirected, end workflow
                            return ;
                        }

                        for ($i = 5; $i >= 1; $i--) {
                            if ($this->active_send_status < $i && !empty($this->auto_responder->{'timeout_reply_message_' . $i}) && $this->auto_responder->{'wait_timeout_reply_' . $i} > 0 && (time() - $this->chat->last_op_msg_time > $this->auto_responder->{'wait_timeout_reply_' . $i}) ) {

                                $this->active_send_status = $i;
                                $this->saveThis();

                                $msg = new erLhcoreClassModelmsg();
                                $msg->msg = trim($this->auto_responder->{'timeout_reply_message_' . $i});
                                $msg->meta_msg = $this->auto_responder->getMeta($this->chat, 'nreply');
                                $msg->chat_id = $this->chat->id;
                                $msg->name_support = $this->chat->user !== false ? $this->chat->user->name_support : ($this->auto_responder->operator != '' ? $this->auto_responder->operator : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Live Support'));
                                $msg->user_id = $this->chat->user_id > 0 ? $this->chat->user_id : - 2;
                                $msg->time = time();

                                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_auto_responder_msg_saved', array('msg' => & $msg, 'chat' => & $this->chat));

                                erLhcoreClassChat::getSession()->save($msg);

                                $this->chat->last_msg_id = $msg->id;
                                $this->chat->updateThis(array('update' => array('last_msg_id')));
                            }
                        }

                    } elseif ($this->chat->last_op_msg_time < $this->chat->last_user_msg_time && $this->chat->last_user_msg_time > 0 && $this->chat->last_op_msg_time > $this->chat->pnd_time ) {

                        $lastMessageTime = self::getLastVisitorMessageTime($this->chat);

                        for ($i = 5; $i >= 1; $i--) {
                            $this->auto_responder->{'timeout_op_reply_message_' . $i};
                            $this->auto_responder->{'wait_op_timeout_reply_' . $i};

                            if ($this->active_send_status < $i && !empty($this->auto_responder->{'timeout_op_reply_message_' . $i}) && $this->auto_responder->{'wait_op_timeout_reply_' . $i} > 0 && (time() - $lastMessageTime > $this->auto_responder->{'wait_op_timeout_reply_' . $i}) ) {

                                $this->active_send_status = $i;
                                $this->saveThis();

                                $msg = new erLhcoreClassModelmsg();
                                $msg->msg = trim($this->auto_responder->{'timeout_op_reply_message_' . $i});
                                $msg->chat_id = $this->chat->id;
                                $msg->name_support = $this->chat->user !== false ? $this->chat->user->name_support : ($this->auto_responder->operator != '' ? $this->auto_responder->operator : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Live Support'));
                                $msg->user_id = $this->chat->user_id > 0 ? $this->chat->user_id : - 2;
                                $msg->meta_msg = (string)$this->auto_responder->getMeta($this->chat, 'nreply_op', $i);
                                $msg->time = time();

                                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_auto_responder_msg_saved', array('msg' => & $msg, 'chat' => & $this->chat));

                                erLhcoreClassChat::getSession()->save($msg);

                                $this->chat->last_msg_id = $msg->id;
                                $this->chat->updateThis(array('update' => array('last_msg_id')));
                            }
                        }

                    } elseif ($this->active_send_status > 0) {
                        $this->active_send_status = 0;
                        $this->saveThis();
                    }

                } elseif ($this->chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_ON_HOLD) {
                    for ($i = 5; $i >= 1; $i--) {
                        if ($this->active_send_status < $i && !empty($this->auto_responder->{'timeout_hold_message_' . $i}) && $this->auto_responder->{'wait_timeout_hold_' . $i} > 0 && (time() - $this->chat->last_op_msg_time > $this->auto_responder->{'wait_timeout_hold_' . $i}) ) {

                            $this->active_send_status = $i;
                            $this->saveThis();

                            $msg = new erLhcoreClassModelmsg();
                            $msg->msg = trim($this->auto_responder->{'timeout_hold_message_' . $i});
                            $msg->meta_msg = $this->auto_responder->getMeta($this->chat, 'onhold');
                            $msg->chat_id = $this->chat->id;
                            $msg->name_support = $this->chat->user !== false ? $this->chat->user->name_support : ($this->auto_responder->operator != '' ? $this->auto_responder->operator : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Live Support'));
                            $msg->user_id = $this->chat->user_id > 0 ? $this->chat->user_id : - 2;
                            $msg->time = time();

                            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_auto_responder_msg_saved', array('msg' => & $msg, 'chat' => & $this->chat));

                            erLhcoreClassChat::getSession()->save($msg);

                            $this->chat->last_msg_id = $msg->id;
                            $this->chat->updateThis(array('update' => array('last_msg_id')));
                        }
                    }
                }
            }
        }
    }

    public static function getLastVisitorMessageTime($chat) {
        $messages = erLhcoreClassModelmsg::getList(array('limit' => 10, 'sort' => 'id DESC', 'filter' => array('chat_id' => $chat->id)));

        $prevMessage = null;
        foreach ($messages as $msg) {
            if ($prevMessage === null) {
                if ($msg->user_id == 0){
                    $prevMessage = $msg;
                }
                continue;
            }

            if ($msg->user_id > 0 && $msg->time <= $chat->last_op_msg_time) {
                return $prevMessage->time;
            }

            if ($msg->user_id == 0) {
                $prevMessage = $msg;
            }
        }

        if ($prevMessage instanceof erLhcoreClassModelmsg){
            return $prevMessage->time;
        }

        return $chat->last_user_msg_time;
    }

    public function __get($var)
    {
        switch ($var) {
            case 'auto_responder':
                $this->auto_responder = erLhAbstractModelAutoResponder::fetch($this->auto_responder_id);
                $this->auto_responder->translateByChat($this->chat->chat_locale, array('user_id' => $this->chat->user_id, 'dep_id' => $this->chat->dep_id));
                return $this->auto_responder;
                break;

            case 'chat':
                $this->chat = erLhcoreClassModelChat::fetch($this->chat_id);
                return $this->chat;
                break;

            default:
                ;
                break;
        }
    }

    public $id = null;

    public $chat_id = null;

    public $auto_responder_id = null;

    public $wait_timeout_send = 0;

    public $pending_send_status = 0;

    public $active_send_status = 0;
}