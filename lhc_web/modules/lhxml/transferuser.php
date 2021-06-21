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
        $Transfer->ctime = time();

        erLhcoreClassTransfer::getSession()->save($Transfer);

        // Store messages
        $msg = new erLhcoreClassModelmsg();
        $msg->chat_id = $Chat->id;
        $msg->user_id = -1;

        $userTo = erLhcoreClassModelUser::fetch($Transfer->transfer_to_user_id);
        $msg->name_support = $userTo->name_support;
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_msg_admin_saved', array('msg' => & $msg, 'chat' => & $Chat, 'user_id' => $userTo->id));
        $userToNick = $msg->name_support;

        $msg->name_support = (string)$currentUser->getUserData()->name_support;
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_msg_admin_saved', array('msg' => & $msg, 'chat' => & $Chat, 'user_id' => $currentUser->getUserID()));
        $msg->msg = (string)$msg->name_support . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferuser', 'has transferred chat to') . ' ' . (string)$userToNick;

        // Save message
        erLhcoreClassChat::getSession()->save($msg);

        // User who transferred chat
        $Chat->last_msg_id = $msg->id;
        $Chat->transfer_uid = $currentUser->getUserID();
        $Chat->saveThis();

		erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chat_transfered', array('chat' => & $Chat, 'transfer' => $Transfer, 'msg' => $msg));
	}
}

exit;
?>