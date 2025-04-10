<?php

header('Content-Type: application/javascript');

$tpl = erLhcoreClassTemplate::getInstance('lhnotifications/downloadworkerop.tpl.php');

$nSettings = erLhcoreClassModelChatConfig::fetch('notifications_settings_op');
$data = (array)$nSettings->data;
$tpl->set('nsettings',$data);

echo $tpl->fetch();

exit;
?>