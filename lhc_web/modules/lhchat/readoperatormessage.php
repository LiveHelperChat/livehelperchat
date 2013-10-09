<?php

header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/readoperatormessage.tpl.php');
$tpl->set('referer','');
$tpl->set('referer_site','');

$userInstance = erLhcoreClassModelChatOnlineUser::handleRequest(array('vid' => (string)$Params['user_parameters_unordered']['vid']));
$tpl->set('visitor',$userInstance);

$department = (int)$Params['user_parameters_unordered']['department'] > 0 ? (int)$Params['user_parameters_unordered']['department'] : false;

$inputData = new stdClass();
$inputData->username = '';
$inputData->question = '';
$inputData->email = '';
$inputData->departament_id = $department;
$inputData->validate_start_chat = false;

$tpl->set('department',$department);

$chat = new erLhcoreClassModelChat();

if (isset($_POST['askQuestion']))
{
    $validationFields = array();
    $validationFields['Question'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    $validationFields['DepartamentID'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1) );
    $validationFields['Email'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'validate_email' );

    $form = new ezcInputForm( INPUT_POST, $validationFields );
    $Errors = array();

    if ( !$form->hasValidData( 'Question' ) || trim($form->Question) == '' ) {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter your message');
    } elseif ($form->hasValidData( 'Question' )) {
        $inputData->question = $form->Question;
    }

    if ($form->hasValidData( 'Question' ) && $form->Question != '' && strlen($form->Question) > 500)
    {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Maximum 500 characters for a message');
    }

    if ($userInstance->requires_email == 1) {
    	if ( !$form->hasValidData( 'Email' ) ) {
    		$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter a valid email address');
    	} else {
    		$inputData->email = $chat->email = $form->Email;
    	}
    }

    if (count($Errors) == 0)
    {
       $chat->time = time();
       $chat->status = 0;
       $chat->setIP();
       $chat->hash = erLhcoreClassChat::generateHash();
       $chat->referrer = isset($_POST['URLRefer']) ? $_POST['URLRefer'] : '';
       $chat->session_referrer = isset($_POST['r']) ? $_POST['r'] : '';

       $chat->nick = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Visitor');

       erLhcoreClassModelChat::detectLocation($chat);

       $chat->dep_id = $inputData->departament_id;

       // Assign default department
       if ($form->hasValidData( 'DepartamentID' ) && erLhcoreClassModelDepartament::getCount(array('filter' => array('id' => $form->DepartamentID))) > 0) {
       		$chat->dep_id = $form->DepartamentID;
       } elseif ($chat->dep_id == 0 || erLhcoreClassModelDepartament::getCount(array('filter' => array('id' => $chat->dep_id))) == 0) {
	       	$departments = erLhcoreClassModelDepartament::getList(array('limit' => 1,'filter' => array('department_transfer_id' => 0)));
	       	$department = array_shift($departments);
	       	$chat->dep_id = $department->id;
       }

       $chat->priority = is_numeric($Params['user_parameters_unordered']['priority']) ? (int)$Params['user_parameters_unordered']['priority'] : $chat->department->priority;
       $chat->chat_initiator = erLhcoreClassModelChat::CHAT_INITIATOR_PROACTIVE;

       // Store chat
       erLhcoreClassChat::getSession()->save($chat);

       // Mark as user has read message from operator.
       $userInstance->message_seen = 1;
       $userInstance->message_seen_ts = time();
       $userInstance->chat_id = $chat->id;
       $userInstance->saveThis();

       $chat->online_user_id = $userInstance->id;

       if ( erLhcoreClassModelChatConfig::fetch('track_footprint')->current_value == 1) {
       		erLhcoreClassModelChatOnlineUserFootprint::assignChatToPageviews($userInstance);
       }

       // Store Message from operator
       $msg = new erLhcoreClassModelmsg();
       $msg->msg = trim($userInstance->operator_message);
       $msg->chat_id = $chat->id;
       $msg->name_support = $userInstance->operator_user !== false ? trim($userInstance->operator_user->name.' '.$userInstance->operator_user->surname) : (!empty($userInstance->operator_user_proactive) ? $userInstance->operator_user_proactive : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support'));
       $msg->user_id = $userInstance->operator_user_id > 0 ? $userInstance->operator_user_id : 1;
       $msg->time = time()-7; // Deduct 7 seconds so for user all looks more natural

       erLhcoreClassChat::getSession()->save($msg);

       // Store User Message
       $msg = new erLhcoreClassModelmsg();
       $msg->msg = trim($inputData->question);
       $msg->chat_id = $chat->id;
       $msg->user_id = 0;
       $msg->time = time();
       erLhcoreClassChat::getSession()->save($msg);

       if ($userInstance->invitation !== false) {

       		if ($userInstance->invitation->wait_message != '') {
		       	$msg = new erLhcoreClassModelmsg();
		       	$msg->msg = trim($userInstance->invitation->wait_message);
		       	$msg->chat_id = $chat->id;
		       	$msg->name_support = $userInstance->operator_user !== false ? trim($userInstance->operator_user->name.' '.$userInstance->operator_user->surname) : (!empty($userInstance->operator_user_proactive) ? $userInstance->operator_user_proactive : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support'));
		       	$msg->user_id = $userInstance->operator_user_id > 0 ? $userInstance->operator_user_id : 1;
		       	$msg->time = time()+5;
		       	erLhcoreClassChat::getSession()->save($msg);
       		}

	       	// Store wait timeout attribute for future
	       	$chat->wait_timeout = $userInstance->invitation->wait_timeout;
	       	$chat->timeout_message = $userInstance->invitation->timeout_message;
       } else {

       		// Default auto responder
	       	$responder = erLhAbstractModelAutoResponder::processAutoResponder();

	       	if ($responder instanceof erLhAbstractModelAutoResponder) {
	       		$chat->wait_timeout = $responder->wait_timeout;
	       		$chat->timeout_message = $responder->timeout_message;

	       		if ($responder->wait_message != '') {
	       			$msg = new erLhcoreClassModelmsg();
	       			$msg->msg = trim($responder->wait_message);
	       			$msg->chat_id = $chat->id;
	       			$msg->name_support = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support');
	       			$msg->user_id = 1;
	       			$msg->time = time()+5;
	       			erLhcoreClassChat::getSession()->save($msg);
	       		}
	       	}
       }

       // Set chat attributes for transfer workflow logic
       if ($chat->department !== false && $chat->department->department_transfer_id > 0) {
	       	$chat->transfer_if_na = 1;
	       	$chat->transfer_timeout_ts = time();
	       	$chat->transfer_timeout_ac = $chat->department->transfer_timeout;
       }

       $chat->last_msg_id = $msg->id;
       $chat->last_user_msg_time = time();
       $chat->saveThis();

       // Redirect user
       erLhcoreClassModule::redirect('chat/chatwidgetchat/' . $chat->id . '/' . $chat->hash);
       exit;

    } else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('input_data',$inputData);

if (isset($_GET['URLReferer']))
{
    $tpl->set('referer',$_GET['URLReferer']);
}

if (isset($_POST['URLRefer']))
{
    $tpl->set('referer',$_POST['URLRefer']);
}

if (isset($_GET['r']))
{
	$tpl->set('referer_site',$_GET['r']);
}

if (isset($_POST['r']))
{
	$tpl->set('referer_site',$_POST['r']);
}


$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'widget';
$Result['dynamic_height'] = true;
$Result['dynamic_height_message'] = 'lhc_sizing_chat';
$Result['pagelayout_css_append'] = 'widget-chat';
