<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhkernel/nopermission.tpl.php');
$tpl->set('module',$Params);
$tpl->set('module_name',self::$currentModuleName);

$Result['content'] = $tpl->fetch();

?>