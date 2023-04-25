<?php

header ( 'content-type: application/json; charset=utf-8' );

$content = 'false';
$content_status = 'false';
$userOwner = 'true';
$chatsGone = [];

$hasAccessToReadArray = array();

if (isset($_POST['chats']) && is_array($_POST['chats']) && count($_POST['chats']) > 0)
{
    $ReturnMessages = array();
    $ReturnStatuses = array();

    $tpl = erLhcoreClassTemplate::getInstance( 'lhchat/syncadmin.tpl.php');
    $currentUser = erLhcoreClassUser::instance();

    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
    	echo json_encode(array('error' => 'true', 'result' => 'Invalid CSRF Token' ));
    	exit;
    }
    
    // Set online condition configurations
    erLhcoreClassChat::$trackActivity = (int)erLhcoreClassModelChatConfig::fetchCache('track_activity')->current_value == 1;
    erLhcoreClassChat::$trackTimeout = (int)erLhcoreClassModelChatConfig::fetchCache('checkstatus_timeout')->current_value;
    erLhcoreClassChat::$onlineCondition = (int)erLhcoreClassModelChatConfig::fetchCache('online_if')->current_value;
        
    // We do not need a session anymore
    session_write_close();
    
    $db = ezcDbInstance::get();        

    $db->beginTransaction();
    try {

        $icons_additional = erLhAbstractModelChatColumn::getList(array('ignore_fields' => array('position','conditions','column_identifier','enabled'), 'sort' => false, 'filter' => array('icon_mode' => 1, 'enabled' => 1, 'chat_enabled' => 1)));
        $see_sensitive_information = $currentUser->hasAccessTo('lhchat','see_sensitive_information');
            
        foreach ($_POST['chats'] as $chat_id_list)
        {
            list($chat_id, $MessageID ) = explode(',',$chat_id_list);
            $chat_id = (int)$chat_id;
            $MessageID = (int)$MessageID;

            $Chat = erLhcoreClassModelChat::fetch($chat_id);

            if (!($Chat instanceof erLhcoreClassModelChat)) {
                $chatsGone[] = $chat_id;
                continue;
            }

            $Chat->updateIgnoreColumns = array('last_msg_id');

            if ( isset($hasAccessToReadArray[$chat_id]) || erLhcoreClassChat::hasAccessToRead($Chat) )
            {
                $hasAccessToReadArray[$chat_id] = true;

                if ( ($Chat->last_msg_id > (int)$MessageID) && count($Messages = erLhcoreClassChat::getPendingMessages($chat_id,$MessageID)) > 0)
                {
                    // If chat had flag that it contains unread messages set to 0
                    if ($Chat->has_unread_messages == 1 || $Chat->unread_messages_informed == 1) {
                         $Chat->has_unread_messages = 0;
                         $Chat->unread_messages_informed = 0;
                         $Chat->updateThis(array('update' => array('has_unread_messages','unread_messages_informed')));
                    }

                    // Auto accept transfered chats if I have opened this chat
                    if ($Chat->status == erLhcoreClassModelChat::STATUS_OPERATORS_CHAT) {

                       $q = $db->createDeleteQuery();

                       // Delete transfered chat's to me
                       $q->deleteFrom( 'lh_transfer' )->where( $q->expr->eq( 'chat_id', $Chat->id ), $q->expr->eq( 'transfer_to_user_id', $currentUser->getUserID() ) );
                       $stmt = $q->prepare();
                       $stmt->execute();
                    }

                    $newMessagesNumber = count($Messages);

                    $tpl->set('messages',$Messages);
                    $tpl->set('chat',$Chat);
                    $tpl->set('current_user_id',$currentUser->getUserID());
                    $tpl->set('see_sensitive_information',$see_sensitive_information);

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

                    if ($msgText != '') {
                        $msgText = erLhcoreClassBBCode::make_plain($msgText);
                    }

                    $response = array('chat_id' => $chat_id,'nck' => $Chat->nick, 'msfrom' => $MessageID, 'msop' => $firstNewMessage['user_id'], 'mn' => $newMessagesNumber, 'msg' => $msgText, 'content' => $templateResult, 'message_id' => $LastMessageIDs['id']);

                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.syncadmin',array('response' => & $response, 'messages' => $Messages, 'chat' => $Chat));

                    $ReturnMessages[] = $response;
                }

                $lp = $Chat->lsync > 0 ? time()-$Chat->lsync : false;

                $vwa = $Chat->status != erLhcoreClassModelChat::STATUS_CLOSED_CHAT && $Chat->last_user_msg_time > ($Chat->last_op_msg_time > 0 ? $Chat->last_op_msg_time : $Chat->pnd_time) && (time() - $Chat->last_user_msg_time > (int)erLhcoreClassModelChatConfig::fetchCache('vwait_to_long')->current_value) ? erLhcoreClassChat::formatSeconds(time() - $Chat->last_user_msg_time) : null;

                $user_typing_txt = $Chat->user_typing_txt;

                if (!$see_sensitive_information && $user_typing_txt != '') {
                    $user_typing_txt = \LiveHelperChat\Models\Abstract\ChatMessagesGhosting::maskMessage($user_typing_txt);
                }

                if ($Chat->is_user_typing == true) {
                    $ReturnStatuses[$chat_id] = array('pnd_rsp' => $Chat->pnd_rsp, 'vwa' => $vwa, 'lmt' => max($Chat->last_user_msg_time, $Chat->last_op_msg_time, $Chat->pnd_time), 'cs' => $Chat->status, 'co' => $Chat->user_id, 'cdur' => $Chat->chat_duration_front, 'lmsg' => erLhcoreClassChat::formatSeconds(time() - ($Chat->last_user_msg_time > 0 ? $Chat->last_user_msg_time : $Chat->time)), 'chat_id' => $chat_id, 'lp' => $lp, 'um' => $Chat->has_unread_op_messages, 'us' => $Chat->user_status_front, 'tp' => 'true','tx' => htmlspecialchars($user_typing_txt));
                } else {
                    $ReturnStatuses[$chat_id] = array('pnd_rsp' => $Chat->pnd_rsp, 'vwa' => $vwa, 'lmt' => max($Chat->last_user_msg_time, $Chat->last_op_msg_time, $Chat->pnd_time), 'cs' => $Chat->status, 'co' => $Chat->user_id, 'cdur' => $Chat->chat_duration_front, 'lmsg' => erLhcoreClassChat::formatSeconds(time() - ($Chat->last_user_msg_time > 0 ? $Chat->last_user_msg_time : $Chat->time)), 'chat_id' => $chat_id, 'lp' => $lp, 'um' => $Chat->has_unread_op_messages, 'us' => $Chat->user_status_front, 'tp' => 'false');
                }

                if ($Chat->operation_admin != '') {
                    $ReturnStatuses[$chat_id]['oad'] = 1;
                }

                if (!empty($icons_additional)) {
                    $chatItems = [$Chat];
                    erLhcoreClassChat::prefillGetAttributes($chatItems, array(), array(), array('additional_columns' => $icons_additional, 'do_not_clean' => true));
                    $chatIcons = [];
                    foreach ($icons_additional as $iconAdditional) {
                        $columnIconData = json_decode($iconAdditional->column_icon,true);
                        if (isset($Chat->{'cc_' . $iconAdditional->id})) {
                            $chatIcons[] = [
                                'has_popup' => $iconAdditional->has_popup,
                                'icon_id' => $iconAdditional->id,
                                'title' => (isset($Chat->{'cc_' . $iconAdditional->id . '_tt'})) ? $Chat->{'cc_' . $iconAdditional->id . '_tt'} : (isset($Chat->{'cc_' . $iconAdditional->id}) ? $Chat->{'cc_' . $iconAdditional->id} : ''),
                                'icon' => ($iconAdditional->column_icon != "" && strpos($iconAdditional->column_icon, '"') !== false) ? $columnIconData[$Chat->{'cc_' . $iconAdditional->id}]['icon'] : $iconAdditional->column_icon,
                                'color' => isset($columnIconData[$Chat->{'cc_' . $iconAdditional->id}]['color']) ? $columnIconData[$Chat->{'cc_' . $iconAdditional->id}]['color'] : '#CECECE'
                            ];
                        }
                   }
                   $ReturnStatuses[$chat_id]['adicons'] = $chatIcons;
                }

            } else {
                $chatsGone[] = $chat_id;
            }

        }
        $db->commit();
    } catch (Exception $e) {
        $db->rollback();
    }

    if (count($ReturnMessages) > 0) {
        $content = $ReturnMessages;
    }

    if (count($ReturnStatuses) > 0) {
        $content_status = $ReturnStatuses;
    }
}

$response = array('error' => 'false','uw' => $userOwner, 'result_status' => $content_status, 'result' => $content);

if (!empty($chatsGone)) {
    $response['cg'] = $chatsGone;
}

echo erLhcoreClassChat::safe_json_encode($response);
exit;
?>