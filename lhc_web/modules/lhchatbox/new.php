<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchatbox/new.tpl.php');

$chatbox = new erLhcoreClassModelChatbox();

if ( isset($_POST['Save']) )
{
	$Errors = erLhcoreClassChatbox::validateChatbox($chatbox);

	if (count($Errors) == 0) {
		$chatbox->chat->saveThis();
		$chatbox->chat->time = time();
		$chatbox->chat_id = $chatbox->chat->id;
		$chatbox->saveThis();
		erLhcoreClassModule::redirect('chatbox/list');
		exit;
	} else {
		$tpl->set('errors',$Errors);
	}
}

if ( isset($_POST['Cancel']) ) {
	erLhcoreClassModule::redirect('chatbox/list');
	exit;
}

$tpl->set('chatbox',$chatbox);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
		array('url' =>erLhcoreClassDesign::baseurl('chatbox/list'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/list','Chatbox')),
		array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/new','New chatbox')));
?>