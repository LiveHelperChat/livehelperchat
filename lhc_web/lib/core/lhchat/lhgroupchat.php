<?php

class erLhcoreClassGroupChat {

    public static function validateGroupChat( & $item)
    {
        $definition = array(
            'Name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'Type' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            )
        );

        $form = new ezcInputForm( INPUT_POST, $definition );

        $Errors = array();

        if ( !$form->hasValidData( 'Name' ) || $form->Name == '' )
        {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please enter a name');
        } else {
            $item->name = $form->Name;
        }

        if ( $form->hasValidData( 'Type' ) && $form->Type == true ) {
            $item->type = true;
        } else {
            $item->type = false;
        }

        return $Errors;
    }

    public static function inviteOperator($groupId, $userId) {

        $member = erLhcoreClassModelGroupChatMember::findOne(array('filter' => array('group_id' => $groupId, 'user_id' => $userId)));

        if (!($member instanceof erLhcoreClassModelGroupChatMember)) {
            $newMember = new erLhcoreClassModelGroupChatMember();
            $newMember->user_id = $userId;
            $newMember->group_id = $groupId;
            $newMember->last_activity = 0;
            $newMember->jtime = 0;
            $newMember->saveThis();
        }
    }

    /*
     * We can cancel invitation only if user has not joined yet.
     * */
    public static function cancelInvite($groupId, $userId)
    {
        $member = erLhcoreClassModelGroupChatMember::findOne(array('filter' => array('jtime' => 0, 'group_id' => $groupId, 'user_id' => $userId)));

        if ($member instanceof erLhcoreClassModelGroupChatMember) {
            $member->removeThis();
        }
    }

    public static function getGroupChatMembers($groupId, $userId) {
        $members = erLhcoreClassModelGroupChatMember::getList(array('limit' => false, 'filter' => array('group_id' => $groupId)));

        foreach ($members as $member) {
            if ($member->user_id == $userId) {
                if ($member->jtime == 0) {
                    $member->jtime = time();
                    $member->updateThis(array('update' => array('jtime')));
                }
            }
        }

        erLhcoreClassChat::prefillGetAttributes($members, array('id','last_activity_ago','user_id','n_off_full','hide_online'), array('user','group_id','last_activity'));

        return array_values($members);
    }

    /**
     * Gets chats messages, used to review chat etc.
     * */
    public static function getChatMessages($chat_id, $limit = 100, $lastMessageId = 0, $order = '>')
    {
        if ($lastMessageId == 0) {
            $db = ezcDbInstance::get();
            $stmt = $db->prepare('SELECT lh_group_msg.* FROM lh_group_msg INNER JOIN ( SELECT id FROM lh_group_msg WHERE chat_id = :chat_id ORDER BY id DESC LIMIT :limit) AS items ON lh_group_msg.id = items.id ORDER BY lh_group_msg.id ASC');
            $stmt->bindValue( ':chat_id',$chat_id,PDO::PARAM_INT);
            $stmt->bindValue( ':limit',$limit,PDO::PARAM_INT);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();
            $rows = $stmt->fetchAll();
        } else {
            $db = ezcDbInstance::get();
            $stmt = $db->prepare('SELECT lh_group_msg.* FROM lh_group_msg INNER JOIN ( SELECT id FROM lh_group_msg WHERE chat_id = :chat_id AND lh_group_msg.id ' . $order . ' :message_id ORDER BY id DESC LIMIT :limit) AS items ON lh_group_msg.id = items.id ORDER BY lh_group_msg.id ASC');
            $stmt->bindValue( ':chat_id',$chat_id,PDO::PARAM_INT);
            $stmt->bindValue( ':limit',$limit,PDO::PARAM_INT);
            $stmt->bindValue( ':message_id',$lastMessageId,PDO::PARAM_INT);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();
            $rows = $stmt->fetchAll();
        }
        return $rows;
    }

    public static function getChatHistory($chat, $lastMessageId)
    {
        $messages = self::getChatMessages($chat->id, erLhcoreClassChat::$limitMessages, $lastMessageId, '<');

        $messageId = 0;
        $hasMessages = true;
        if (count($messages) == erLhcoreClassChat::$limitMessages) {
            reset($messages);
            $message = current($messages);
            $messageId = $message['id'];
        } else {
            $hasMessages = false;
        }

        $msop = $lmsop = 0;

        if (!empty($messages)) {

            // Messages
            reset($messages);
            $message = current($messages);
            $msop = $message['user_id'];

            // Messages
            end($messages);
            $message = current($messages);
            $lmsop = $message['user_id'];
        }

        return array(
            'message_id' => $messageId,
            'messages' => $messages,
            'has_messages' => $hasMessages,
            'lmsop' => $lmsop,
            'msop' => $msop,
        );
    }
}

?>