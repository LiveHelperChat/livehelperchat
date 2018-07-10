<?php

erLhcoreClassRestAPIHandler::setHeaders();

erLhcoreClassRestAPIHandler::outputResponse(array(
    'isonline' => erLhcoreClassChat::isOnline(
        (int)$Params['user_parameters']['department_id'],
        true,
        array (
            'online_timeout' => (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'],
            'exclude_bot' => (isset($_GET['exclude_bot']) && $_GET['exclude_bot'] == 'true'),
            'ignore_user_status' => (isset($_GET['ignore_user_status']) && $_GET['ignore_user_status'] == 'true')
        )
    )
));

exit();