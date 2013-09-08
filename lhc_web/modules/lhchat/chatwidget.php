<?php

// For IE to support headers if chat is installed on different domain
header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');

$embedMode = false;
$modeAppend = '';
if ((string)$Params['user_parameters_unordered']['mode'] == 'embed') {
	$embedMode = true;
	$modeAppend = '/(mode)/embed';
}

if (($hashSession = CSCacheAPC::getMem()->getSession('chat_hash_widget')) !== false) {

    list($chatID,$hash) = explode('_',$hashSession);

    // Redirect user
    erLhcoreClassModule::redirect('chat/chatwidgetchat','/' . $chatID . '/' . $hash . $modeAppend );
    exit;
}

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/chatwidget.tpl.php');
$tpl->set('referer','');

$tpl->set('append_mode',$modeAppend);

// Start chat field options
$startData = erLhcoreClassModelChatConfig::fetch('start_chat_data');
$startDataFields = (array)$startData->data;

$inputData = new stdClass();
$inputData->username = isset($_GET['prefill']['username']) ? (string)$_GET['prefill']['username'] : '';
$inputData->question = '';
$inputData->email = isset($_GET['prefill']['email']) ? (string)$_GET['prefill']['email'] : '';
$inputData->phone = isset($_GET['prefill']['phone']) ? (string)$_GET['prefill']['phone'] : '';
$inputData->departament_id = (int)$Params['user_parameters_unordered']['department'];
$inputData->validate_start_chat = false;
$inputData->name_items = array();
$inputData->value_items = array();
$inputData->value_sizes = array();
$inputData->value_types = array();
$inputData->priority = is_numeric($Params['user_parameters_unordered']['priority']) ? (int)$Params['user_parameters_unordered']['priority'] : false;

$chat = new erLhcoreClassModelChat();

// Assign department instantly
if ($inputData->departament_id > 0) {
	$chat->dep_id = $inputData->departament_id;
}

// Leave a message functionality
$leaveamessage = (string)$Params['user_parameters_unordered']['leaveamessage'] == 'true' ? true : false;

$additionalParams = array();
if ((string)$Params['user_parameters_unordered']['offline'] == 'true' && $leaveamessage == true) {
	$additionalParams['offline'] = true;
}

$tpl->set('leaveamessage',$leaveamessage);

// Department functionality
$department = (int)$Params['user_parameters_unordered']['department'] > 0 ? (int)$Params['user_parameters_unordered']['department'] : false;
$tpl->set('department',$department);



if (isset($_POST['StartChat']))
{
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
	       $chat->session_referrer = isset($_SESSION['lhc_site_referrer']) ? $_SESSION['lhc_site_referrer'] : '';

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

	                if ( erLhcoreClassModelChatConfig::fetch('track_footprint')->current_value == 1) {
	            		erLhcoreClassModelChatOnlineUserFootprint::assignChatToPageviews($userInstance);
	            	}
	            }
	       }

	       // Store message if required
	       if (isset($startDataFields['message_visible_in_page_widget']) && $startDataFields['message_visible_in_page_widget'] == true) {
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

	       // Store hash if user reloads page etc, we show widget
	       CSCacheAPC::getMem()->setSession('chat_hash_widget',$chat->id.'_'.$chat->hash);

	       // Store hash for user previous chat, so user after main chat close can reopen old chat
	       CSCacheAPC::getMem()->setSession('chat_hash_widget_resume',$chat->id.'_'.$chat->hash);

	       // Redirect user
	       erLhcoreClassModule::redirect('chat/chatwidgetchat','/' . $chat->id . '/' . $chat->hash . $modeAppend);
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
$Result['pagelayout'] = 'widget';
$Result['dynamic_height'] = true;
$Result['dynamic_height_message'] = 'lhc_sizing_chat';
$Result['pagelayout_css_append'] = 'widget-chat';

if ($embedMode == true) {
	$Result['dynamic_height_message'] = 'lhc_sizing_chat_page';
	$Result['pagelayout_css_append'] = 'embed-widget';
}


?>