<?php

$docShare = erLhcoreClassModelDocShare::fetch($Params['user_parameters']['id']);
$currentUser = erLhcoreClassUser::instance();

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
	die('Invalid CSRF Token');
	exit;
}

if ($currentUser->hasAccessTo('lhdocshare','deleteglobaldoc') || ($currentUser->hasAccessTo('lhdocshare','deletedoc') && $docShare->user_id == $currentUser->getUserID()))
{
	$docShare->removeThis();	
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;


?>