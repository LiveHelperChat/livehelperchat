<?php

header ( 'content-type: application/json; charset=utf-8' );

$content = [];
$content_status = [];
$userOwner = 'true';

$hasAccessToReadArray = array();

$payload = json_decode(file_get_contents('php://input'),true);

if (is_array($payload) && count($payload) > 0)
{
    $ReturnMessages = array();
    $ReturnStatuses = array();

    $tpl = erLhcoreClassTemplate::getInstance( 'lhchat/syncadmin.tpl.php');
    $currentUser = erLhcoreClassUser::instance();

    // We do not need a session anymore
    session_write_close();

    $db = ezcDbInstance::get();

            foreach ($payload as $chat_id_list)
            {

                list($chat_id, $MessageID, $lastGroupSync) = explode(',',$chat_id_list);

            $chat_id = (int)$chat_id;
            $MessageID = (int)$MessageID;

            $Chat = erLhcoreClassModelGroupChat::fetch($chat_id);
            $Chat->updateIgnoreColumns = array('last_msg_id');

                $hasAccessToReadArray[$chat_id] = true;

                if ( ($Chat->last_msg_id > (int)$MessageID) && count($Messages = erLhcoreClassGroupChat::getChatMessages($chat_id, erLhcoreClassChat::$limitMessages, $MessageID)) > 0)
                {

                    foreach ($Messages as $messageIndex => $message) {
                        $Messages[$messageIndex] = $message;
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

                    $response = array('chat_id' => $chat_id, 'nck' => $Chat->nick, 'msfrom' => $MessageID, 'msop' => $firstNewMessage['user_id'], 'lmsop' => $LastMessageIDs['user_id'], 'mn' => $newMessagesNumber, 'msg' => $msgText, 'content' => $templateResult, 'message_id' => $LastMessageIDs['id']);

                    $ReturnMessages[] = $response;
                }

                if ($lastGroupSync < time() - 15 || $Chat->last_msg_id > (int)$MessageID) {
                    // Update last activity group member
                    $q = ezcDbInstance::get()->createUpdateQuery();
                    $q->update( 'lh_group_chat_member' )
                        ->set('last_activity',time())
                        ->set('last_msg_id',$Chat->last_msg_id)
                        ->where(
                            $q->expr->eq( 'user_id', $currentUser->getUserID() ),
                            $q->expr->eq( 'group_id', $chat_id )
                        );
                    $stmt = $q->prepare();
                    $stmt->execute();
                }

                if ($lastGroupSync < time() - 15) {
                    $resultStatusItem = array(
                        'chat_id' => $chat_id,
                        'lgsync' => time(),
                        'operators' => erLhcoreClassGroupChat::getGroupChatMembers($Chat->id, $currentUser->getUserID())
                    );

                    if ($Chat->type == erLhcoreClassModelGroupChat::PRIVATE_CHAT && $Chat->user_id != $currentUser->getUserID()) {
                        $validUser = false;
                        foreach ($resultStatusItem['operators'] as $operator) {
                            if ($operator->user_id == $currentUser->getUserID()) {
                                $validUser = true;
                                break;
                            }
                        }

                        // As this means user tried to read private messages
                        // Delete it's response
                        if ($validUser === false) {
                            $ReturnMessages = [];
                        }
                    }

                    // It was first call we have to check does chat has older messages?
                    if ($MessageID == 0 && isset($newMessagesNumber)) {
                        $resultStatusItem['has_more_messages'] = $newMessagesNumber == erLhcoreClassChat::$limitMessages;
                        $resultStatusItem['old_message_id'] = $firstNewMessage['id'];
                    }

                    $ReturnStatuses[] = $resultStatusItem;
                }
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