<?php

$user = erLhcoreClassModelUser::fetch((int)$Params['user_parameters']['id']);

$ts = time();
$hash = sha1($user->id . '_' . $user->password . '_' . erConfigClassLhConfig::getInstance()->getSetting('site','secrethash') . '_' . $ts);

erLhcoreClassUser::instance()->logout();
erLhcoreClassModule::redirect('user/loginasuser','/' . $user->id . '/(hash)/' . $hash . '/(ts)/' . $ts);

exit;

?>