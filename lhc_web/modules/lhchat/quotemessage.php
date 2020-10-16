<?php

header ( 'content-type: application/json; charset=utf-8' );

try {
    $msg = erLhcoreClassModelmsg::fetch($Params['user_parameters']['id']);
    $chat = erLhcoreClassModelChat::fetch($msg->chat_id);

    if ( erLhcoreClassChat::hasAccessToRead($chat) )
    {
        $array = array();
        $array['msg'] = preg_replace('#\[translation\](.*?)\[/translation\]#is', '', $msg->msg);
        $array['error'] = 'f';

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_quote_admin_returned',array('response' => & $array));

        echo json_encode($array);
    }

} catch (Exception $e) {
    echo json_encode(array('error' => 't'));
}

exit;

?>