<?php

header('X-Frame-Options: DENY');

$lhUser = erLhcoreClassUser::instance();

if (!$lhUser->isLogged() || !$lhUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.logout',array('user' => & $lhUser));

$lhUser->logout();

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.after_logout',array('user' => & $lhUser));

//erLhcoreClassModule::redirect('user/login');
exit;

?>