<?php

$tpl = erLhcoreClassTemplate::getInstance('lhaudit/logrecord.tpl.php');

$log = erLhAbstractModelAudit::fetch($Params['user_parameters']['id']);

$tpl->set('object', $log);

echo $tpl->fetch();
exit;

?>