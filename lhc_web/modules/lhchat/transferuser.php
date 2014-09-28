<?php


if (is_numeric( $Params['user_parameters']['chat_id']) && is_numeric($Params['user_parameters']['item_id']))
{

	$Chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
	if ( erLhcoreClassChat::hasAccessToRead($Chat) )
	{
		$currentUser = erLhcoreClassUser::instance();

	    $Transfer = new erLhcoreClassModelTransfer();
	    $Transfer->chat_id = $Chat->id;

	    if ( isset($_POST['type']) && $_POST['type'] == 'dep' ) {
	    	$Transfer->dep_id = $Params['user_parameters']['item_id']; // Transfer was made to department
	    } else {
	    	$Transfer->transfer_to_user_id = $Params['user_parameters']['item_id']; // Transfer was made to user
	    }

	    // Original department id
	    $Transfer->from_dep_id = $Chat->dep_id;

	    // User which is transfering
	    $Transfer->transfer_user_id = $currentUser->getUserID();

	    erLhcoreClassTransfer::getSession()->save($Transfer);

	    $tpl = erLhcoreClassTemplate::getInstance('lhkernel/alert_success.tpl.php');
	    if ( isset($_POST['type']) && $_POST['type'] == 'dep' ) {
	    	$tpl->set('msg',erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferuser','Chat was assigned to selected department'));
	    } else {
	    	$tpl->set('msg',erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferuser','Chat was assigned to selected user'));
	    }

	    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chat_transfered',array('chat' => & $Chat));
	    
	    echo json_encode(array('error' => 'false', 'result' => $tpl->fetch(), 'chat_id' => $Params['user_parameters']['chat_id']));
	    exit;
	}
}
exit;
?>