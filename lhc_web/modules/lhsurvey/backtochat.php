<?php 

try {

    if (is_numeric((string)$Params['user_parameters']['chat_id']) && $Params['user_parameters']['chat_id'] > 0) {

        if ((string)$Params['user_parameters']['hash'] != '') {
            $hash = $Params['user_parameters']['hash'];
        }

        if (is_numeric($Params['user_parameters']['chat_id'])) {
            $chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);
        }

    } else if ((string)$Params['user_parameters']['hash'] != '') {
        list($chatID,$hash) = explode('_',$Params['user_parameters']['hash']);
        $chat = erLhcoreClassModelChat::fetch($chatID);
    }

    erLhcoreClassChat::setTimeZoneByChat($chat);

    if ($chat->hash == $hash)
    {
        $survey = erLhAbstractModelSurvey::fetch($Params['user_parameters']['survey']);
        
        if ($survey instanceof erLhAbstractModelSurvey) {
            // Change to default status
            $chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_DEFAULT;
                     
            $surveyItem = erLhAbstractModelSurveyItem::getInstance($chat, $survey);

            $msgAppend = '';

            // If filled change status to temp, so next time user goes to survey form he can continue to fill it.
            if ($surveyItem->is_filled == true) {
                $surveyItem->status = erLhAbstractModelSurveyItem::STATUS_TEMP;
                $surveyItem->saveOrUpdate();
                $msgAppend = '[survey="'. $surveyItem->survey_id . '_' . $surveyItem->id .'"]';
            }
            
            $msg = new erLhcoreClassModelmsg();
            $msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/backtochat', 'Visitor has been redirected back to chat!') . " " . $msgAppend;
            $msg->chat_id = $chat->id;
            $msg->user_id = - 1;
            
            $chat->last_user_msg_time = $msg->time = time();
            
            erLhcoreClassChat::getSession()->save($msg);
                                    
            // Set last message ID
            $chat->last_msg_id = $msg->id;
              
            if ($chat->has_unread_messages == 1 && $chat->last_user_msg_time < (time() - 5)) {
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.unread_chat',array('chat' => & $chat));
            }
            
            $chat->has_unread_messages = 1;
            
            $chat->saveThis();

            echo json_encode(array('result' => true));
            
            flush();
             
            session_write_close();
             
            if ( function_exists('fastcgi_finish_request') ) {
                fastcgi_finish_request();
            };
            
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('survey.back_to_chat',array('chat' => & $chat, 'msg' => & $msg));
        }        
    }
    
} catch(Exception $e) {
   $tpl->setFile('lhchat/errors/chatnotexists.tpl.php');
}

exit;
?>