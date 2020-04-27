<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/previewmessage.tpl.php');
$tpl->set('msg',(isset($_POST['msg']) ? $_POST['msg'] : null));
$tpl->set('message_body',isset($_POST['msg_body']));

echo $tpl->fetch();
exit;

?>