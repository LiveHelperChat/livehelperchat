<?php

class erLhcoreClassMailconvWorkflow {

    public static function closeConversation($params) {

        $conv = $params['conv'];

        if (!isset($params['force_update']) && $conv->status == erLhcoreClassModelMailconvConversation::STATUS_CLOSED) {
            return;
        }

        if (isset($params['user_id']) && is_numeric($params['user_id'])) {
            if ($conv->user_id == 0 || (isset($params['force_user_change']) && $params['force_user_change'] === true)) {
                $conv->user_id = $params['user_id'];
            }
        }

        if ($conv->cls_time == 0) {
            $conv->cls_time = time();
        }

        if ($conv->accept_time == 0) {
            $conv->accept_time = time();
        }

        $conv->wait_time = $conv->accept_time - $conv->pnd_time;

        $conv->response_time = $conv->lr_time - $conv->accept_time;
        $conv->interaction_time = $conv->cls_time - $conv->accept_time;
        $conv->status = erLhcoreClassModelMailconvConversation::STATUS_CLOSED;
        $messages = erLhcoreClassModelMailconvMessage::getList(['limit' => false, 'sort' => 'udate ASC', 'filter' => ['conversation_id' => $conv->id]]);
        $conv->conv_duration = self::getConversationDuration($messages);
        $conv->saveThis();

        foreach ($messages as $message) {

            if ($message->status != erLhcoreClassModelMailconvMessage::STATUS_RESPONDED)
            {
                $message->status = erLhcoreClassModelMailconvMessage::STATUS_RESPONDED;
                $message->response_type = erLhcoreClassModelMailconvMessage::RESPONSE_NOT_REQUIRED;
                $message->user_id = $conv->user_id; // In case we are changing message status always update user_id
            }

            if ($message->lr_time == 0) {
                $message->lr_time = time();
            }

            if ($message->cls_time == 0) {
                $message->cls_time = time();
            }

            // Happens if operator replies being invisible so no accept_time is set
            if ($message->accept_time == 0) {
                $message->accept_time = $message->ctime;
            }

            if ($message->response_time == 0 && $message->lr_time >= $message->accept_time) {
                $message->response_time = $message->lr_time - $message->accept_time;
            }

            if ($message->interaction_time == 0 && $message->cls_time >= $message->accept_time) {
                $message->interaction_time = $message->cls_time - $message->accept_time;
            }

            if ($message->wait_time == 0 && $message->accept_time >= $message->ctime) {
                $message->wait_time = $message->accept_time - $message->ctime;
            }

            // Assign user id to conversation id if it was not assigned,
            // This is required in case the operator just closes conversation.
            // The Same conversation can have multiple operators working on ticket
            // We want to keep any previously set user_id
            if ($message->user_id == 0) {
                $message->user_id = $conv->user_id;
            }

            // Update always conversation user to present conversation user
            $message->conv_user_id = $conv->user_id;

            $message->updateThis();
        }

        $conv->user_id > 0 && erLhcoreClassChat::updateActiveChats($conv->user_id);

        if ($conv->department !== false) {
            erLhcoreClassChat::updateDepartmentStats($conv->department);
        }
    }

    public static function getConversationDuration($messages, $attr = 'ctime') {
        $previousMessage = null;
        $timeToAdd = 0;
        foreach ($messages as $message) {

            if ($previousMessage == null) {
                $previousMessage = $message;
                continue;
            }

            // Include difference only between different messages
            if (($previousMessage->response_type == erLhcoreClassModelMailconvMessage::RESPONSE_INTERNAL && in_array($message->response_type,array(
                    erLhcoreClassModelMailconvMessage::RESPONSE_NORMAL,
                    erLhcoreClassModelMailconvMessage::RESPONSE_NOT_REQUIRED,
                    erLhcoreClassModelMailconvMessage::RESPONSE_UNRESPONDED
                ))) ||
                ($message->response_type == erLhcoreClassModelMailconvMessage::RESPONSE_INTERNAL && in_array($previousMessage->response_type,array(
                    erLhcoreClassModelMailconvMessage::RESPONSE_NORMAL,
                    erLhcoreClassModelMailconvMessage::RESPONSE_NOT_REQUIRED,
                    erLhcoreClassModelMailconvMessage::RESPONSE_UNRESPONDED
                )))) {

                if ($previousMessage->conv_duration == 0) {
                    $diff = $message->{$attr} - $previousMessage->{$attr};
                } else {
                    $diff = $previousMessage->conv_duration;
                }

                if ($previousMessage->conv_duration == 0) {
                    $previousMessage->conv_duration = $diff;
                    $previousMessage->updateThis(array('update' => array('conv_duration')));
                }

                $timeToAdd += $diff;
            }

            $previousMessage = $message;
        }
        return $timeToAdd;
    }

    /*
     * Check does email whois owner going to change has personal mailbox,
     * and it belongs to personal mailbox group
     * */
    public static function changePersonalMailbox($mail, $newUserId)
    {
        $personalMailboxes = erLhcoreClassModelMailconvPersonalMailboxGroup::getList(['customfilter' => ["JSON_EXTRACT(mails, '$.{$mail->mailbox_id}') IS NOT NULL"], 'filter' => ['active' => 1]]);
        foreach ($personalMailboxes as $personalMailbox) {
            foreach ($personalMailbox->mails_array as $mailboxId => $userId) {
                if ($newUserId == $userId && $mail->mailbox_id != $mailboxId) {
                    $mail->mailbox_id = $mailboxId;
                    $mail->updateThis(['update' => ['mailbox_id']]);
                    break;
                }
            }
        }
    }

    public static function logInteraction($message, $nameSupport, $conversationId) {
        $msg = new erLhcoreClassModelMailconvMessageInternal();
        $msg->time = time();
        $msg->chat_id = $conversationId;
        $msg->user_id = -1;
        $msg->name_support = $nameSupport;
        $msg->msg = $message;
        $msg->saveThis();
    }
}

?>