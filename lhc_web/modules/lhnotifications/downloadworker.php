<?php

$tpl = erLhcoreClassTemplate::getInstance('lhnotifications/downloadworker.tpl.php');

$nSettings = erLhcoreClassModelChatConfig::fetch('notifications_settings');
$data = (array)$nSettings->data;
$tpl->set('nsettings',$data);

header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-Disposition: attachment; filename = sw.lhc.js");
header("Content-Type: application/octet-stream");
header("Content-Transfer-Encoding: binary");

echo $tpl->fetch();

exit;
?>