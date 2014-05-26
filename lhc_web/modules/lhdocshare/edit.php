<?php

$tpl = erLhcoreClassTemplate::getInstance('lhdocshare/edit.tpl.php');

$docShare = erLhcoreClassModelDocShare::fetch($Params['user_parameters']['id']);

//erLhcoreClassDocShare::covertToPDF($docShare); 
erLhcoreClassDocShare::convertPDFToPNG($docShare);


if ( isset($_POST['Update']) )
{
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect();
		exit;
	}
	
	$Errors = erLhcoreClassDocShare::validateDocShare($docShare);

	if (count($Errors) == 0) {
		$docShare->saveThis();
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
$Result['content'] = $tpl->fetch();
$Result['path'] = array(
		array('url' =>erLhcoreClassDesign::baseurl('docshare/list'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/list','Documents sharer')),
		array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/edit','Document edit')));
?>