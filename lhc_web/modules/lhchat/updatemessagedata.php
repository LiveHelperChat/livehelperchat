<?php

erLhcoreClassRestAPIHandler::setHeaders();

if (!empty($_GET) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $requestPayload = $_GET;
} else {
    $requestPayload = json_decode(file_get_contents('php://input'),true);
}

try {

    if ( !isset($Params['user_parameters']['chat_id']) || !isset($Params['user_parameters']['hash']) || !isset($Params['user_parameters']['msg_id'])) {
        throw new Exception('Chat ID or message ID not provided!');
    }

    $chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

    if ($chat instanceof erLhcoreClassModelChat && $chat->hash === $Params['user_parameters']['hash'])
    {
        $msg = erLhcoreClassModelmsg::fetch($Params['user_parameters']['msg_id']);

        if ($msg instanceof erLhcoreClassModelmsg && $msg->chat_id == $chat->id) {

            if (isset($requestPayload['action'])) {
                if ($requestPayload['action'] == 'iframe_close') {
                    $metaMsg = $msg->meta_msg_array;
                    $metaMsg['content']['seen_content'] = true;
                    $metaMsg['content']['iframe'] = null;
                    $msg->meta_msg = json_encode($metaMsg);
                    $msg->updateThis(['update' => ['meta_msg']]);
                }
            }
        }
    }

} catch ( Exception $e ) {

}

exit;

?>