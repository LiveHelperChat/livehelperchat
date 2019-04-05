<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/previewmessage.tpl.php');

$msg = new stdClass();
$msg->msg = (isset($_POST['msg']) ? $_POST['msg'] : null);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_msg_admin_saved',array('msg' => & $msg));

$tpl->set('msg',$msg->msg);

echo $tpl->fetch();
exit;

?>