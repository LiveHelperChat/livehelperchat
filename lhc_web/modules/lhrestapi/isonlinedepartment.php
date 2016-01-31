<?php
header('content-type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

erLhcoreClassRestAPIHandler::outputResponse(array(
    'isonline' => erLhcoreClassChat::isOnline((int) $Params['user_parameters']['department_id'], true, array(
        'online_timeout' => (int) erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout']
    ))
));

exit();