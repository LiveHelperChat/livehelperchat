<?php

header ( 'content-type: application/json; charset=utf-8' );

$content = [];
$content_status = 'false';
$userOwner = 'true';

$hasAccessToReadArray = array();

$payload = json_decode(file_get_contents('php://input'),true);

if (is_array($payload) && count($payload) > 0)
{
    $ReturnMessages = array();
    $ReturnStatuses = array();

    $tpl = erLhcoreClassTemplate::getInstance( 'lhchat/syncadmin.tpl.php');
    $currentUser = erLhcoreClassUser::instance();

    /*if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        echo json_encode(array('error' => 'true', 'result' => 'Invalid CSRF Token' ));
        exit;
    }*/

    // We do not need a session anymore
    session_write_close();

    $db = ezcDbInstance::get();

            foreach ($payload as $chat_id_list)
            {
            list($chat_id, $MessageID ) = explode(',',$chat_id_list);
            $chat_id = (int)$chat_id;
            $MessageID = (int)$MessageID;

            $Chat = erLhcoreClassModelGroupChat::fetch($chat_id);
            $Chat->updateIgnoreColumns = array('last_msg_id');

                $hasAccessToReadArray[$chat_id] = true;

                if ( ($Chat->last_msg_id > (int)$MessageID) && count($Messages = erLhcoreClassModelGroupMsg::getList(array('filter' => array('chat_id' => $chat_id), 'filtergt' => array('id' => $MessageID)))) > 0)
                {

                    foreach ($Messages as $messageIndex => $message) {
                        $Messages[$messageIndex] = $message->getState();
                    }

                    $newMessagesNumber = count($Messages);

                    $tpl->set('messages',$Messages);
                    $tpl->set('chat',$Chat);
                    $tpl->set('current_user_id',$currentUser->getUserID());

                    $msgText = '';
                    if ($userOwner == 'true') {
                        foreach ($Messages as $msg) {
                            if ($msg['user_id'] != $currentUser->getUserID()) {
                                $userOwner = 'false';
                                $msgText = $msg['msg'];
                                break;
                            }
                        }
                    }
                    // Get first message opertor id
                    reset($Messages);
                    $firstNewMessage = current($Messages);

                    // Get last message
                    end($Messages);
                    $LastMessageIDs = current($Messages);

                    // Fetch content
                    $templateResult = $tpl->fetch();

                    $response = array('chat_id' => $chat_id,'nck' => $Chat->nick, 'msfrom' => $MessageID, 'msop' => $firstNewMessage['user_id'], 'mn' => $newMessagesNumber, 'msg' => $msgText, 'content' => $templateResult, 'message_id' => $LastMessageIDs['id']);

                    $ReturnMessages[] = $response;
                }

                $lp = $Chat->lsync > 0 ? time()-$Chat->lsync : false;

                $ReturnStatuses[$chat_id] = array('cs' => $Chat->status, 'co' => $Chat->user_id, 'chat_id' => $chat_id, 'lp' => $lp, 'um' => $Chat->has_unread_op_messages);
            }

        if (count($ReturnMessages) > 0) {
            $content = $ReturnMessages;
        }

        if (count($ReturnStatuses) > 0) {
            $content_status = $ReturnStatuses;
        }
}



echo erLhcoreClassChat::safe_json_encode(array('error' => 'false','uw' => $userOwner, 'result_status' => $content_status, 'result' => $content ));
exit;
?>