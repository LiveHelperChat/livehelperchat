<?php

$tpl = erLhcoreClassTemplate::getInstance('lhdocshare/view.tpl.php');

$docShare = erLhcoreClassModelDocShare::fetch($Params['user_parameters']['id']);

if ($docShare->active == 0 && (!erLhcoreClassUser::instance()->isLogged() || !erLhcoreClassUser::instance()->hasAccessTo('lhdocshare', 'manage_dc'))) {	
	erLhcoreClassModule::redirect();
	exit;
}

$tpl->set('docshare',$docShare);
$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'docshare';


?>