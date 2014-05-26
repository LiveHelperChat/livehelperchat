<?php

$tpl = erLhcoreClassTemplate::getInstance('lhdocshare/view.tpl.php');

$docShare = erLhcoreClassModelDocShare::fetch($Params['user_parameters']['id']);
$tpl->set('docshare',$docShare);
$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'docshare';


?>