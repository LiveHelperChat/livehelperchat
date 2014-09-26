<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchatbox/edit.tpl.php');

$chatbox = erLhcoreClassModelChatbox::fetch($Params['user_parameters']['id']);

if ( isset($_POST['Update']) )
{
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect();
		exit;
	}

	$Errors = erLhcoreClassChatbox::validateChatbox($chatbox);

	if (count($Errors) == 0) {
		// Update
		$chatbox->saveThis();

		// Update chat
		$chatbox->chat->updateThis();
		$tpl->set('updated',true);
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
		array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Edit')));
?>