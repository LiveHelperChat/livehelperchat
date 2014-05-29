<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhdocshare/embedcode.tpl.php');
$docShare = erLhcoreClassModelDocShare::fetch($Params['user_parameters']['id']);
$tpl->set('docshare',$docShare);
$cfgSite = erConfigClassLhConfig::getInstance();
$tpl->set('locales',$cfgSite->getSetting( 'site', 'available_site_access' ));
echo $tpl->fetch();
exit;

?>