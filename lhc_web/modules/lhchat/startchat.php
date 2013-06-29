<?php

if (($hashSession = CSCacheAPC::getMem()->getSession('chat_hash_widget')) !== false) {

    list($chatID,$hash) = explode('_',$hashSession);

    // Remove chat from chat widget, from now user will be communicating using popup window
    CSCacheAPC::getMem()->setSession('chat_hash_widget',false);

    // Redirect user
    erLhcoreClassModule::redirect('chat/chat/' . $chatID . '/' . $hash);
    exit;
}

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/startchat.tpl.php');
$tpl->set('referer','');

// Start chat field options
$startData = erLhcoreClassModelChatConfig::fetch('start_chat_data');
$startDataFields = (array)$startData->data;

// Input fields holder
$inputData = new stdClass();
$inputData->username = isset($_GET['prefill']['username']) ? (string)$_GET['prefill']['username'] : '';
$inputData->question = '';
$inputData->email = isset($_GET['prefill']['email']) ? (string)$_GET['prefill']['email'] : '';
$inputData->phone = isset($_GET['prefill']['phone']) ? (string)$_GET['prefill']['phone'] : '';
$inputData->departament_id = 0;
$inputData->validate_start_chat = true;
$inputData->name_items = array();
$inputData->value_items = array();
$inputData->value_types = array();
$inputData->value_sizes = array();

$chat = new erLhcoreClassModelChat();

$leaveamessage = (string)$Params['user_parameters_unordered']['leaveamessage'] == 'true' ? true : false;

$additionalParams = array();
if ((string)$Params['user_parameters_unordered']['offline'] == 'true' && $leaveamessage == true) {
	$additionalParams['offline'] = true;
}

$tpl->set('leaveamessage',$leaveamessage);

if (isset($_POST['StartChat'])) {
   // Validate post data
   $Errors = erLhcoreClassChatValidator::validateStartChat($inputData,$startDataFields,$chat,$additionalParams);

   if (count($Errors) == 0)
   {
   		if (isset($additionalParams['offline']) && $additionalParams['offline'] == true) {
	   		erLhcoreClassChatMail::sendMailRequest($inputData,$chat);
	   		$tpl->set('request_send',true);
	   	} else {
	       $chat->time = time();
	       $chat->status = 0;
	       $chat->setIP();
	       $chat->hash = erLhcoreClassChat::generateHash();
	       $chat->referrer = isset($_POST['URLRefer']) ? $_POST['URLRefer'] : '';

	       if ( empty($chat->nick) ) {
	           $chat->nick = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Visitor');
	       }

	       erLhcoreClassModelChat::detectLocation($chat);

	       // Store chat
	       erLhcoreClassChat::getSession()->save($chat);

	       // Assign chat to user
	       if ( erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value == 1 ) {
	            // To track online users
	            $userInstance = erLhcoreClassModelChatOnlineUser::handleRequest();

	            if ($userInstance !== false) {
	                $userInstance->chat_id = $chat->id;
	                $userInstance->saveThis();
	            }
	       }

	       // Store message if required
	       if (isset($startDataFields['message_visible_in_popup']) && $startDataFields['message_visible_in_popup'] == true) {
	           if ( $inputData->question != '' ) {
	               // Store question as message
	               $msg = new erLhcoreClassModelmsg();
	               $msg->msg = trim($inputData->question);
	               $msg->chat_id = $chat->id;
	               $msg->user_id = 0;
	               $msg->time = time();

	               erLhcoreClassChat::getSession()->save($msg);
	           }
	       }

	       if (isset($_SESSION[$_SERVER['REMOTE_ADDR']]['form'])){
	       		unset($_SESSION[$_SERVER['REMOTE_ADDR']]['form']);
	       }

	       // Redirect user
	       erLhcoreClassModule::redirect('chat/chat/' . $chat->id . '/' . $chat->hash);
	       exit;
	   	}
    } else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('start_data_fields',$startDataFields);

$definition = array(
		'name'  => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',
				null,
				FILTER_REQUIRE_ARRAY
		),
		'value' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',
				null,
				FILTER_REQUIRE_ARRAY
		),
		'type' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'string',
				null,
				FILTER_REQUIRE_ARRAY
		),
		'size' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'string',
				null,
				FILTER_REQUIRE_ARRAY
		)
);

$form = new ezcInputForm( INPUT_GET, $definition );

if ( $form->hasValidData( 'name' ) && !empty($form->name))
{
	$inputData->name_items = $form->name;
}

if ( $form->hasValidData( 'value' ) && !empty($form->value))
{
	$inputData->value_items = $form->value;
}

if ( $form->hasValidData( 'type' ) && !empty($form->type))
{
	$inputData->value_types = $form->type;
}

if ( $form->hasValidData( 'size' ) && !empty($form->size))
{
	$inputData->value_sizes = $form->size;
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

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'userchat';

$Result['path'] = array(array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Fill in the form to start a chat')))

?>