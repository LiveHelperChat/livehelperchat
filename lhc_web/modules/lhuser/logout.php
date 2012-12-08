<?php

$lhUser = erLhcoreClassUser::instance();
$lhUser->logout();

erLhcoreClassModule::redirect('user/login');
return ;

?>