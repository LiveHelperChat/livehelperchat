<?php

header ( 'content-type: application/json; charset=utf-8' );

try {
    $msg = erLhcoreClassModelmsg::fetch($Params['user_parameters']['id']);
    $chat = erLhcoreClassModelChat::fetch($msg->chat_id);

    if ( erLhcoreClassChat::hasAccessToRead($chat) )
    {
        $array = array();

        if ($Params['user_parameters_unordered']['type'] == 'group') {

            $previousMessages = erLhcoreClassModelmsg::getList(array('limit' => 10, 'sort' => 'id DESC','filterlt' => array('id' => $msg->id)));
            $groupMessages = [];
            foreach ($previousMessages as $prevMessage) {
                if ($prevMessage->user_id == $msg->user_id || $prevMessage->user_id == -1) {
                    if ($prevMessage->user_id == $msg->user_id && trim($prevMessage->msg) != '') {
                        $groupMessages[] = trim($prevMessage->msg);
                    }
                } else {
                    break;
                }
            }
            $groupMessages = array_reverse($groupMessages);
            $groupMessages[] = $msg->msg;

            $previousMessages = erLhcoreClassModelmsg::getList(array('limit' => 10, 'sort' => 'id ASC','filtergt' => array('id' => $msg->id)));
            foreach ($previousMessages as $prevMessage) {
                if ($prevMessage->user_id == $msg->user_id || $prevMessage->user_id == -1) {
                    if ($prevMessage->user_id == $msg->user_id && trim($prevMessage->msg) != '') {
                        $groupMessages[] = trim($prevMessage->msg);
                    }
                } else {
                    break;
                }
            }

            $array['msg'] = implode("\n",$groupMessages);
        } else {
            $array['msg'] = preg_replace('#\[translation\](.*?)\[/translation\]#is', '', $msg->msg);
        }

        $array['error'] = 'f';

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_quote_admin_returned',array('response' => & $array));

        echo json_encode($array);
    }

} catch (Exception $e) {
    echo json_encode(array('error' => 't'));
}

exit;

?>