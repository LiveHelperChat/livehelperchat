<?php

$tpl = new erLhcoreClassTemplate( 'lhchat/chatwidget.tpl.php');

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'chattabs';

?>