<?php 

// Accepting a chat logs an operator in without a permission check, so it's allowed only on a back office site access
if (!erLhcoreClassUserUtils::validateLoginSiteAccess()) {
	erLhcoreClassModule::redirect('user/login');
	exit;
}

$cfgSite = erConfigClassLhConfig::getInstance();
$secretHash = $cfgSite->getSetting( 'site', 'secrethash' );

if (erLhcoreClassModelChatAccept::validateEmailVerificationHash($Params['user_parameters']['email'], $secretHash, $Params['user_parameters']['validation_hash'])) {
	$accept = erLhcoreClassModelChatAccept::fetchByHash($Params['user_parameters']['hash']);
	if ($accept !== false) {						
		$chat_id = $accept->chat_id;			
		if ($accept->wused == 0) {
			$userID = erLhcoreClassModelUser::fetchUserByEmail($Params['user_parameters']['email'],(trim($Params['user_parameters']['email']) != '' ? trim($Params['user_parameters']['email']) : false));	
						
			$userAccept = erLhcoreClassUserUtils::fetchAutologinUser($userID);

			if ( $userAccept !== false && $accept->ctime > (time() - erLhcoreClassModelChatConfig::fetch('accept_chat_link_timeout')->current_value ) ) {
				$accept->wused = 1;
				$accept->saveThis();
				
				erLhcoreClassUser::instance()->setLoggedUser($userAccept->id);
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