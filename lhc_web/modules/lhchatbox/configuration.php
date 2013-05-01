<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchatbox/configuration.tpl.php');

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/list','Chatbox')));

?>