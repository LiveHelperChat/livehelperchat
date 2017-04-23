<?php 
header('content-type: application/json; charset=utf-8');

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
	echo json_encode(array('error' => 'true', 'result' => 'Invalid CSRF Token' ));
	exit;
}

$definition = array(
		'data' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
		)
);

$form = new ezcInputForm( INPUT_POST, $definition );

$onlineUser = erLhcoreClassModelChatOnlineUser::fetch($Params['user_parameters']['online_user_id']);

if ($form->hasValidData('data')) {
	$onlineUser->notes = $form->data;
	$onlineUser->saveThis();
}

echo json_encode(array());
exit;
?>