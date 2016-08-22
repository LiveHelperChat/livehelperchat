<?php
header('content-type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

if ($Params['user_parameters_unordered']['hash'] != '' || $Params['user_parameters_unordered']['vid'] != '') {

	$checkHash = true;
	$vid = false;
	$chatID = 0;
	$chat = false;
	
	if ($Params['user_parameters_unordered']['sharemode'] == 'onlineuser' && $Params['user_parameters_unordered']['vid'] != '') {
	    $vid = erLhcoreClassModelChatOnlineUser::fetchByVid($Params['user_parameters_unordered']['vid']);
	    $checkHash = false;	    	    
	} elseif ($Params['user_parameters_unordered']['hash'] != '') {
		list($chatID,$hash) = explode('_',$Params['user_parameters_unordered']['hash']);
	} else if ($Params['user_parameters_unordered']['hash_resume'] != '') {		
		list($chatID,$hash) = explode('_',$Params['user_parameters_unordered']['hash_resume']);
	} elseif ($Params['user_parameters_unordered']['vid'] != '') {				
		$vid = erLhcoreClassModelChatOnlineUser::fetchByVid($Params['user_parameters_unordered']['vid']);
		if ($vid !== false) {
			$chatID = $vid->chat_id;
			$checkHash = false;			
		} else {			
			echo json_encode(array('stored' => 'false', 'disableShare' => 'false'));
			exit;
		}
	};
	

	try {		
		if ($chatID > 0) {
			$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $chatID);
		} else {
			$chat = false;
		}

		if ( (($checkHash == true && $chat !== false && $chat->hash == $hash) || $checkHash == false) && ( is_object($vid) || ($chat !== false && $chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT))) {
            if (isset($_POST['data'])) {
                $errors = array();
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('cobrowse.before_store_node_map', array('data' => json_decode($_POST['data']), 'errors' => & $errors));

                if(empty($errors)) {
                    erLhcoreClassCoBrowse::addModifications($chat !== false ? $chat : $vid, $_POST['data'], array('base' => isset($_GET['url']) ? $_GET['url'] : ''));
                } else {
                    if ($chat instanceof erLhcoreClassModelChat) {
                        $msg = new erLhcoreClassModelmsg();
                        $msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('cobrowse/browse', 'Co-browse is stopped by error') . ': ' . implode(';', $errors);
                        $msg->chat_id = $chat->id;
                        $msg->user_id = -1;
                        $msg->time = time();
                        erLhcoreClassChat::getSession()->save($msg);

                        if ($chat->last_msg_id < $msg->id) {
                            $chat->last_msg_id = $msg->id;
                            $chat->last_user_msg_time = $msg->time;
                            $chat->saveThis();
                        }

                        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.sync_back_office');
                    }

                    array_unshift($errors, erTranslationClassLhTranslation::getInstance()->getTranslation('cobrowse/browse', 'Co-browse is stopped!'));
                    echo json_encode(array('stored' => 'false', 'disableShare' => 'true', 'error_msg' => implode(PHP_EOL, $errors)));
                    exit;
                }

			}
		}
	} catch (Exception $e) {
		print_r($e);
		// Do nothing
	}
}

echo json_encode(array('stored' => 'false', 'disableShare' => 'false'));
exit;
?>