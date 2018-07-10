<?php

erLhcoreClassRestAPIHandler::setHeaders();

$chat = erLhcoreClassModelChat::fetch((int)$Params['user_parameters']['chat_id']);

try {

    if ($chat instanceof erLhcoreClassModelChat) {
        erLhcoreClassRestAPIHandler::outputResponse(array(
            'isonline' => erLhcoreClassChat::isOnline(
                (int)$chat->dep_id,
                true,
                array (
                    'online_timeout' => (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'],
                    'exclude_bot' => (isset($_GET['exclude_bot']) && $_GET['exclude_bot'] == 'true'),
                    'ignore_user_status' => (isset($_GET['ignore_user_status']) && $_GET['ignore_user_status'] == 'true')
                )
            )
        ));
    } else {
        throw new Exception('Chat could not be found!');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}
exit();