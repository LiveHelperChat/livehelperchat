<?php

erLhcoreClassRestAPIHandler::setHeaders();

if (!isset($_GET['wopen']) || (isset($_GET['isproactive']) && $_GET['isproactive'] == 1 && $_GET['wopen'] == 1)) {
    if (isset($_GET['dep'])) {
        $department = explode(',', $_GET['dep']);
        erLhcoreClassChat::validateFilterIn($department);
    } else {
        $department = false;
    }

    $isOnlineHelp = erLhcoreClassChat::isOnline($department, false, array(
        'ignore_user_status' => (int)erLhcoreClassModelChatConfig::fetch('ignore_user_status')->current_value,
        'online_timeout' => (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout']));

    erLhcoreClassRestAPIHandler::outputResponse(array('change_status' => true, 'online' => $isOnlineHelp));
} else {
    erLhcoreClassRestAPIHandler::outputResponse(array('change_status' => false));
}

if (erLhcoreClassModelChatConfig::fetch('track_is_online')->current_value && !isset($_GET['dot'])) {
    $ignorable_ip = erLhcoreClassModelChatConfig::fetch('ignorable_ip')->current_value;
    if ( $ignorable_ip == '' || !erLhcoreClassIPDetect::isIgnored(erLhcoreClassIPDetect::getIP(),explode(',',$ignorable_ip))) {
        if ((string)$_GET['vid'] != '') {
            $db = ezcDbInstance::get();

            $resetActivity = ", operation = '', operation_chat = ''";

            // wopen do not execute any commands while widget is open
            if (!isset($_GET['wopen']))
            {
                /**
                 * Perhaps there is some pending operations for online visitor
                 * */
                $stmt = $db->prepare('SELECT operation FROM lh_chat_online_user WHERE vid = :vid');
                $stmt->bindValue(':vid',(string)$_GET['vid']);
                $stmt->execute();
                $operation = $stmt->fetch(PDO::FETCH_COLUMN);
                $resetActivity = '';
            }

            $stmt = $db->prepare("UPDATE lh_chat_online_user SET last_check_time = :time{$resetActivity}, user_active = :user_active WHERE vid = :vid");
            $stmt->bindValue(':time',time(),PDO::PARAM_INT);
            $stmt->bindValue(':vid',(string)$_GET['vid']);
            $stmt->bindValue(':user_active',(isset($_GET['uactiv']) ? 1 : 0),PDO::PARAM_INT);
            $stmt->execute();

            // If nodejs is used we have to inform back office operators about changed statuses
            if (isset($_GET['uaction']) && (string)$_GET['uaction'] == 1 && isset($_GET['hash'])) {
                if (strpos((string)$_GET['hash'], '_') !== false) {
                    list($chatId) = explode('_', (string)$_GET['hash']);
                }

                if (isset($chatId) && is_numeric($chatId)) {
                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.data_changed_chat',array('chat_id' => $chatId));
                }
            }
        }
    }
}

exit;
?>