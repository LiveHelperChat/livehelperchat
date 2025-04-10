<?php

$tpl = erLhcoreClassTemplate::getInstance('lhnotifications/downloadworkerop.tpl.php');

$nSettings = erLhcoreClassModelChatConfig::fetch('notifications_settings_op');
$data = (array)$nSettings->data;
$tpl->set('nsettings',$data);

header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-Disposition: attachment; filename = sw.lhc.bo.js");
header("Content-Type: application/octet-stream");
header("Content-Transfer-Encoding: binary");

echo $tpl->fetch();

exit;
?>