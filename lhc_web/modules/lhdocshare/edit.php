<?php

$tpl = erLhcoreClassTemplate::getInstance('lhdocshare/edit.tpl.php');

$docShare = erLhcoreClassModelDocShare::fetch($Params['user_parameters']['id']);

if ( isset($_POST['Update']) )
{
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect();
		exit;
	}
	
	$Errors = erLhcoreClassDocShare::validateDocShare($docShare);

	if (count($Errors) == 0) {
		$docShare->saveThis();
		erLhcoreClassDocShare::makeConversion($docShare);
		$tpl->set('updated',true);
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
		array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/edit','Document edit')));
?>