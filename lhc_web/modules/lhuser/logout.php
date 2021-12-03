<?php

header('X-Frame-Options: DENY');

$lhUser = erLhcoreClassUser::instance();

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.logout',array('user' => & $lhUser));

$lhUser->logout();

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.after_logout',array('user' => & $lhUser));

erLhcoreClassModule::redirect('user/login');
exit;

?>