<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/statistic.tpl.php');

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Statistic')))

?>