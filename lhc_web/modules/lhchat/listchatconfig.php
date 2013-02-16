<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/listchatconfig.tpl.php');

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','List chat configuration')));

?>