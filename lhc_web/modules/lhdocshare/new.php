<?php

$tpl = erLhcoreClassTemplate::getInstance('lhdocshare/new.tpl.php');

$docShare = new erLhcoreClassModelDocShare();

if ( isset($_POST['Save']) )
{
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect();
		exit;
	}

	$Errors = erLhcoreClassDocShare::validateDocShare($docShare);

	if (count($Errors) == 0) {
		$docShare->user_id = $currentUser->getUserID();
		$docShare->saveThis();		
		erLhcoreClassDocShare::makeConversion($docShare);		
		erLhcoreClassModule::redirect('docshare/list');
		exit;
	} else {
		$tpl->set('errors',$Errors);
	}
}

if ( isset($_POST['Cancel']) ) {
	erLhcoreClassModule::redirect('docshare/list');
	exit;
}

$tpl->set('docshare',$docShare);

$docSharer = erLhcoreClassModelChatConfig::fetch('doc_sharer');
$data = (array)$docSharer->data;
$tpl->set('share_data',$data);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
		array('url' => erLhcoreClassDesign::baseurl('docshare/index'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/index','Documents sharer')),
		array('url' =>erLhcoreClassDesign::baseurl('docshare/list'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/list','Documents list')),
		array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/new','New document')));
?>