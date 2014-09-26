<?php
$currentUser = erLhcoreClassUser::instance();
if (!$currentUser->isLogged() && !$currentUser->authenticate($_POST['username'],$_POST['password']))
{
    exit;
}

try {
	$visitor = erLhcoreClassModelChatOnlineUser::fetch((int)$Params['user_parameters']['online_id']);
} catch (Exception $e) {
	exit;
}

if ( isset($_POST['msg']) && trim($_POST['msg']) != '') {

	$validationFields = array();
	$validationFields['msg'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
	$form = new ezcInputForm( INPUT_POST, $validationFields );

	$visitor->operator_message = $form->msg;
	$visitor->message_seen = 0;
	$visitor->operator_user_id = $currentUser->getUserID();
	$visitor->saveThis();
}

exit;
?>