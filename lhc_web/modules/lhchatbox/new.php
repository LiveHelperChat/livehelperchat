<?php

$chatbox = new erLhcoreClassModelChatbox();

$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chatbox.new', array('chatbox' => $chatbox));

$tpl = erLhcoreClassTemplate::getInstance('lhchatbox/new.tpl.php');

if ( isset($_POST['Save']) )
{
	$Errors = erLhcoreClassChatbox::validateChatbox($chatbox);

	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect();
		exit;
	}

	if (count($Errors) == 0) {

		// Predefine some default variables
		$departments = erLhcoreClassModelDepartament::getList();
		$ids = array_keys($departments);
		$id = array_shift($ids);
		$chatbox->chat->dep_id = $id;
		$chatbox->chat->time = time();
		$chatbox->chat->saveThis();

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
		array('url' =>erLhcoreClassDesign::baseurl('chatbox/configuration'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/list','Chatbox')),
		array('url' =>erLhcoreClassDesign::baseurl('chatbox/list'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/list','Chatbox list')),
		array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/new','New')));
?>