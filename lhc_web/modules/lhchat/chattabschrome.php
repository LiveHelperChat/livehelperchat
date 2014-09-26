<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/chattabschrome.tpl.php');
$tpl->set('currentUser',$currentUser);
$tpl->set('is_popup',$Params['user_parameters_unordered']['mode'] == 'popup');
$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'chattabschrome';

?>