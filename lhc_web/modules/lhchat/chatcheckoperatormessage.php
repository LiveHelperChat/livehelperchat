<?php

header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');
header("Content-type: text/javascript");

$tpl = erLhcoreClassTemplate::getInstance('lhchat/chatcheckoperatormessage.tpl.php');

$userInstance = erLhcoreClassModelChatOnlineUser::handleRequest();

if ($userInstance !== false) {
    $tpl->set('visitor',$userInstance);
    echo $tpl->fetch();
}
exit;
?>