<?php

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
$currentUser = erLhcoreClassUser::instance();    

if (($currentUser->hasAccessTo('lhchat','allowblockusers') || $chat->user_id == $currentUser->getUserID()))
{
    $chat->blockUser();
    echo json_encode(array('msg' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockuser','User was blocked!')));
} else {
    echo json_encode(array('msg' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockuser','User blocking failed, perhaps you do not have permission to block users?')));
}

exit;

?>