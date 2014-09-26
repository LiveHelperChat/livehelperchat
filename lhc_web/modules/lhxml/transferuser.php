<?php
$currentUser = erLhcoreClassUser::instance();
if (!$currentUser->isLogged() && !$currentUser->authenticate($_POST['username'],$_POST['password']))
{
    exit;
}

$Chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

if ( erLhcoreClassChat::hasAccessToRead($Chat) )
{
	if (is_numeric( $Params['user_parameters']['chat_id']) && is_numeric($Params['user_parameters']['user_id']))
	{
		$Transfer = new erLhcoreClassModelTransfer();
		$Transfer->chat_id = $Params['user_parameters']['chat_id'];
		$Transfer->transfer_to_user_id = $Params['user_parameters']['user_id'];
		$Transfer->from_dep_id = $Chat->dep_id;
		$Transfer->transfer_user_id = $currentUser->getUserID();

		erLhcoreClassTransfer::getSession()->save($Transfer);
		
		erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chat_transfered',array('chat' => & $Chat));
	}
}

exit;
?>