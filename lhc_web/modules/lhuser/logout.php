<?php

$lhUser = erLhcoreClassUser::instance();

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.logout',array('user' => & $lhUser));

$lhUser->logout();

erLhcoreClassModule::redirect('user/login');
exit;

?>