<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/previewmessage.tpl.php');
$tpl->set('msg',(isset($_POST['msg']) ? $_POST['msg'] : null));

echo $tpl->fetch();
exit;

?>