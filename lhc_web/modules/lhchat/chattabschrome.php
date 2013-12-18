<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/chattabschrome.tpl.php');
$tpl->set('currentUser',$currentUser);
$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'chattabschrome';

?>