<?php 

$cfgSite = erConfigClassLhConfig::getInstance();
$secretHash = $cfgSite->getSetting( 'site', 'secrethash' );

if ($Params['user_parameters']['validation_hash'] == sha1(sha1($Params['user_parameters']['email'].$secretHash).$secretHash)){
	$userID = erLhcoreClassModelUser::fetchUserByEmail($Params['user_parameters']['email']);		
	if ($user !== false) {		
		$accept = erLhcoreClassModelChatAccept::fetchByHash($Params['user_parameters']['hash']);				
		if ($accept !== false) {
			$chat_id = $accept->chat_id;			
			erLhcoreClassUser::instance()->setLoggedUser($userID);
			$accept->removeThis();
			erLhcoreClassModelChatAccept::cleanup();
			
			erLhcoreClassModule::redirect('chat/single','/'.$chat_id);
			exit;
		}		
	}
}

echo "Invalid request";
exit;
?>