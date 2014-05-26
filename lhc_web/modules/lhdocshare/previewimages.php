<?php

$tpl = erLhcoreClassTemplate::getInstance('lhdocshare/previewimages.tpl.php');

$docShare = erLhcoreClassModelDocShare::fetch($Params['user_parameters']['id']);
$tpl->set('docshare',$docShare);
echo $tpl->fetch();
exit;

?>