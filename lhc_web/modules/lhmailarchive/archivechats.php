<?php

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
	echo json_encode(array('error' => 'true', 'result' => 'Invalid CSFR Token' ));
	exit;
}

$archive = new \LiveHelperChat\Models\mailConv\Archive\Range();

$definition = array(
		'id' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1)
		)
);

$form = new ezcInputForm( INPUT_POST, $definition );
$Errors = array();

if ( !$form->hasValidData( 'id' ) ) {
	echo json_encode(array('error' => 'true', 'result' => 'Invalid archive ID' ));
	exit;
} else {
	$archiveChat = \LiveHelperChat\Models\mailConv\Archive\Range::fetch($form->id);
	$status = $archiveChat->process();

	$tpl = erLhcoreClassTemplate::getInstance( 'lhmailarchive/archivechats.tpl.php');
	$tpl->set('status', $status);
	$tpl->set('archive', $archiveChat);
	$status['result'] = $tpl->fetch();

	echo json_encode($status);
}

exit;

?>