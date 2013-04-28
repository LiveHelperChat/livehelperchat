<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchatbox/configuration.tpl.php');



$Result['content'] = $tpl->fetch();
$Result['path'] = array(
		array('url' => erLhcoreClassDesign::baseurl('chatbox/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/list','Chatbox')),
		array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/htmlcode','Configuration')))

?>