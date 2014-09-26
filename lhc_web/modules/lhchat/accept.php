<?php 

$cfgSite = erConfigClassLhConfig::getInstance();
$secretHash = $cfgSite->getSetting( 'site', 'secrethash' );

if ($Params['user_parameters']['validation_hash'] == sha1(sha1($Params['user_parameters']['email'].$secretHash).$secretHash)) {
	$accept = erLhcoreClassModelChatAccept::fetchByHash($Params['user_parameters']['hash']);
	if ($accept !== false) {						
		$chat_id = $accept->chat_id;			
		if ($accept->wused == 0) {
			$userID = erLhcoreClassModelUser::fetchUserByEmail($Params['user_parameters']['email'],(trim($Params['user_parameters']['email']) != '' ? trim($Params['user_parameters']['email']) : false));	
						
			if ( $userID !== false && $accept->ctime > (time() - erLhcoreClassModelChatConfig::fetch('accept_chat_link_timeout')->current_value ) ) {
				$accept->wused = 1;
				$accept->saveThis();
				
				erLhcoreClassUser::instance()->setLoggedUser($userID);
				erLhcoreClassModule::redirect('chat/single','/'.$chat_id);
				exit;
			} else {				
				erLhcoreClassModule::redirect('user/login','/(r)/'.rawurlencode(base64_encode('chat/single/'.$chat_id)));
				exit;
			}
		} else {			
			erLhcoreClassModule::redirect('user/login','/(r)/'.rawurlencode(base64_encode('chat/single/'.$chat_id)));
			exit;
		}						
		erLhcoreClassModelChatAccept::cleanup();
	}
}

erLhcoreClassModule::redirect('user/login');
exit;

?>