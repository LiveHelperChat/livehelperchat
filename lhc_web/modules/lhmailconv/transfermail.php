<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/transferchat.tpl.php');

$tpl->set('chat', erLhcoreClassModelMailconvConversation::fetch($Params['user_parameters']['id']));
$currentUser = erLhcoreClassUser::instance();
$tpl->set('user_id',$currentUser->getUserID());
$tpl->set('transferMode','mail');

print $tpl->fetch();
exit;

?>