<?php

header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');

$tpl = erLhcoreClassTemplate::getInstance('lhchat/chatcheckoperatormessage.tpl.php');

$userInstance = erLhcoreClassModelChatOnlineUser::handleRequest();

if ($userInstance !== false) {
    $tpl->set('visitor',$userInstance);
    echo $tpl->fetch();
}
exit;
?>