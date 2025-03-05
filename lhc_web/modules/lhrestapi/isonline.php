<?php

erLhcoreClassRestAPIHandler::setHeaders();

erLhcoreClassRestAPIHandler::outputResponse(array(
    'isonline' => erLhcoreClassChat::isOnline(false, false, array(
        'online_timeout' => (int) erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'],
        'ignore_user_status' => (isset($_GET['ignore_user_status']) && $_GET['ignore_user_status'] == 'true'),
        'include_users' => (isset($_GET['include_users']) && $_GET['include_users'] == 'true')
    ))
));

exit();