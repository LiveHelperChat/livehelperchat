<?php

if ($Params['user_parameters_unordered']['sound'] !== null && is_numeric($Params['user_parameters_unordered']['sound'])) {
	erLhcoreClassModelUserSetting::setSetting('chat_message',(int)$Params['user_parameters_unordered']['sound'] == 1 ? 1 : 0);
}

$themeAppend = '';

if (isset($Params['user_parameters_unordered']['theme']) && (int)$Params['user_parameters_unordered']['theme'] > 0){
	try {
		$theme = erLhAbstractModelWidgetTheme::fetch($Params['user_parameters_unordered']['theme']);
		$Result['theme'] = $theme;
		$themeAppend = '/(theme)/'.$theme->id;
	} catch (Exception $e) {

	}
} else {
	$defaultTheme = erLhcoreClassModelChatConfig::fetch('default_theme_id')->current_value;
	if ($defaultTheme > 0) {
		try {
			$theme = erLhAbstractModelWidgetTheme::fetch($defaultTheme);
			$Result['theme'] = $theme;
			$themeAppend = '/(theme)/'.$theme->id;
		} catch (Exception $e) {
			
		}
	}
}

// er
if (isset($Params['user_parameters_unordered']['er']) && (int)$Params['user_parameters_unordered']['er'] == 1) {
    $Result['er'] = true;
    $themeAppend .= '/(er)/1';
}

if (is_array($Params['user_parameters_unordered']['ua']) && !empty($Params['user_parameters_unordered']['ua'])) {
    $themeAppend .= '/(ua)/'.implode('/', $Params['user_parameters_unordered']['ua']);
}

if (isset($Params['user_parameters_unordered']['survey']) && is_numeric($Params['user_parameters_unordered']['survey'])) {
    $themeAppend .= '/(survey)/'. (int)$Params['user_parameters_unordered']['survey'];
}

// Paid chat workflow
if ((string)$Params['user_parameters_unordered']['phash'] != '' && (string)$Params['user_parameters_unordered']['pvhash'] != '') {

    $sound = is_numeric($Params['user_parameters_unordered']['sound']) ? '/(sound)/'.$Params['user_parameters_unordered']['sound'] : '';

    if (isset($Params['user_parameters_unordered']['survey']) && is_numeric($Params['user_parameters_unordered']['survey'])) {
        $themeAppend .= '/(survey)/' . $Params['user_parameters_unordered']['survey'];
    };

    $paidChatSettings = erLhcoreClassChatPaid::paidChatWorkflow(array(
        'uparams' => $Params['user_parameters_unordered'],
        'append_mode' => $themeAppend . $sound,
        'mode' => 'chat'
    ));

    if (isset($paidChatSettings['need_store']) && $paidChatSettings['need_store'] == true) {
        $themeAppend .= '/(phash)/'.htmlspecialchars($Params['user_parameters_unordered']['phash']).'/(pvhash)/'.htmlspecialchars($Params['user_parameters_unordered']['pvhash']);
    }
}

// Perhaps it's direct argument
if ((string)$Params['user_parameters_unordered']['hash'] != '' && (!isset($paidChatSettings) || $paidChatSettings['need_store'] == false)) {
	list($chatID,$hash) = explode('_',$Params['user_parameters_unordered']['hash']);
	
	// Redirect user
	erLhcoreClassModule::redirect('chat/chat/' . $chatID . '/' . $hash . $themeAppend);
	exit;
}

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/startchat.tpl.php');
$tpl->set('referer','');
$tpl->set('referer_site','');
$disabled_department = false;

if (isset($Result['theme'])){
    $tpl->set('theme',$Result['theme']);
}

if (is_array($Params['user_parameters_unordered']['department']) && erLhcoreClassModelChatConfig::fetch('hide_disabled_department')->current_value == 1){
	try {
		
		erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['department']);				
		$departments = erLhcoreClassModelDepartament::getList(array('filterin' => array('id' => $Params['user_parameters_unordered']['department'])));
		
		$disabledAll = true;
		foreach ($departments as $department){
			if ($department->disabled == 0) {
				$disabledAll = false;
			}
		}
		
		// Disable only if all provided departments are disabled
		if ($disabledAll == true){
			$disabled_department = true;
		}
		
	} catch (Exception $e) {
		exit;
	}
}

$tpl->set('disabled_department',$disabled_department);
$tpl->set('append_mode_theme',$themeAppend);

$inputData = new stdClass();
$inputData->chatprefill = '';
$inputData->email = '';
$inputData->username = '';
$inputData->phone = '';
$inputData->ua = $Params['user_parameters_unordered']['ua'];
$inputData->product_id = '';

if (is_array($Params['user_parameters_unordered']['department']) && count($Params['user_parameters_unordered']['department']) == 1) {
	erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['department']);
	$inputData->departament_id = array_shift($Params['user_parameters_unordered']['department']);
} else {
	$inputData->departament_id = 0;
}

if (is_numeric($inputData->departament_id) && $inputData->departament_id > 0 && ($startDataDepartment = erLhcoreClassModelChatStartSettings::findOne(array('filter' => array('department_id' => $inputData->departament_id)))) !== false) {
    $startDataFields = $startDataDepartment->data_array;
} else {
    // Start chat field options
    $startData = erLhcoreClassModelChatConfig::fetch('start_chat_data');
    $startDataFields = (array)$startData->data;
}

// Allow extension override start chat fields
erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.startchat_data_fields',array('data_fields' => & $startDataFields, 'params' => $Params));

if (is_array($Params['user_parameters_unordered']['department'])) {
	erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['department']);
	$inputData->departament_id_array = $Params['user_parameters_unordered']['department'];
}

if (is_array($Params['user_parameters_unordered']['prod'])) {
    erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['prod']);
    $inputData->product_id_array = $Params['user_parameters_unordered']['prod'];
}

$inputData->accept_tos = false;
$inputData->operator = (int)$Params['user_parameters_unordered']['operator'];

// Perhaps user was redirected to leave a message form because chat was not acceptend in some time interval
if ((string)$Params['user_parameters_unordered']['chatprefill'] != '') {
	list($chatID,$hash) = explode('_',$Params['user_parameters_unordered']['chatprefill']);

	try {
		$chatPrefill = erLhcoreClassModelChat::fetch($chatID);
		if ($chatPrefill->hash == $hash) {
			$inputData->chatprefill = $Params['user_parameters_unordered']['chatprefill'];			
			$inputData->username = $chatPrefill->nick;
			$inputData->departament_id = $chatPrefill->dep_id;
			$inputData->email = $chatPrefill->email;			
			$inputData->phone = $chatPrefill->phone;	
			$inputData->accept_tos = true;
		} else {
			unset($chatPrefill);
		}
	} catch (Exception $e) {
		// Do nothing
	} 
}

// Input fields holder
$inputData->username = isset($_GET['prefill']['username']) ? (string)$_GET['prefill']['username'] : $inputData->username;
$inputData->question = isset($_GET['prefill']['question']) ? (string)$_GET['prefill']['question'] : (isset($_GET['prefillMsg']) ? (string)$_GET['prefillMsg'] : '');
$inputData->email = isset($_GET['prefill']['email']) ? (string)$_GET['prefill']['email'] : $inputData->email;
$inputData->phone = isset($_GET['prefill']['phone']) ? (string)$_GET['prefill']['phone'] : $inputData->phone;
$inputData->priority = is_numeric($Params['user_parameters_unordered']['priority']) ? (int)$Params['user_parameters_unordered']['priority'] : false;
$inputData->validate_start_chat = true;
$inputData->name_items = array();
$inputData->value_items = array();
$inputData->value_types = array();
$inputData->value_sizes = array();
$inputData->encattr = array();
$inputData->via_encrypted = array();
$inputData->value_items_admin = array(); // These variables get's filled from start chat form settings
$inputData->via_hidden = array(); // These variables get's filled from start chat form settings
$inputData->hattr = array();
$inputData->hash_resume = false;
$inputData->vid = false;

// Perhaps it's direct argument
if ((string)$Params['user_parameters_unordered']['hash_resume'] != '') {
	CSCacheAPC::getMem()->setSession('chat_hash_widget_resume',(string)$Params['user_parameters_unordered']['hash_resume'],true,true);
	$inputData->hash_resume = (string)$Params['user_parameters_unordered']['hash_resume'];
}

if ((string)$Params['user_parameters_unordered']['vid'] != '') {
	$inputData->vid = (string)$Params['user_parameters_unordered']['vid'];
}

$chat = new erLhcoreClassModelChat();

// Assign department instantly
if ($inputData->departament_id > 0) {
	$chat->dep_id = $inputData->departament_id;
	$tpl->set('department',$chat->dep_id);
} else {
	$tpl->set('department',false);
}

if (isset($startDataFields['requires_dep']) && $startDataFields['requires_dep'] == true && ((!isset($inputData->departament_id_array) || empty($inputData->departament_id_array)) && $inputData->departament_id == 0)) {
    $tpl->set('department_invalid',true);
} elseif (isset($startDataFields['requires_dep']) && $startDataFields['requires_dep'] == true && isset($startDataFields['requires_dep_lock']) && $startDataFields['requires_dep_lock'] == true) {
	if (!isset($_COOKIE['lhc_ldep'])) {
    	setcookie('lhc_ldep', $inputData->departament_id > 0 ? $inputData->departament_id : implode(',',$inputData->departament_id_array),0,'/');
	} elseif (isset($_COOKIE['lhc_ldep']) && $_COOKIE['lhc_ldep'] != ($inputData->departament_id > 0 ? $inputData->departament_id : implode(',',$inputData->departament_id_array))) {
        $tpl->set('department_invalid',true);
	}
}

$leaveamessage = ((string)$Params['user_parameters_unordered']['leaveamessage'] == 'true' || (isset($startDataFields['force_leave_a_message']) && $startDataFields['force_leave_a_message'] == true)) ? true : false;
$tpl->set('forceoffline',false);

$additionalParams = array();
if ((string)$Params['user_parameters_unordered']['offline'] == 'true' && $leaveamessage == true) {
	$additionalParams['offline'] = true;
	$tpl->set('forceoffline',true);
}

// Theme
if (isset($Result['theme'])) {
    $additionalParams['theme'] = $Result['theme'];
}

$tpl->set('leaveamessage',$leaveamessage);

if (isset($_POST['StartChat']) && $disabled_department === false) {
   // Validate post data
   $Errors = erLhcoreClassChatValidator::validateStartChat($inputData,$startDataFields,$chat,$additionalParams);

	erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_chat_started',array('chat' => & $chat, 'errors' => & $Errors, 'offline' => (isset($additionalParams['offline']) && $additionalParams['offline'] == true)));

	if (count($Errors) == 0 && !isset($_POST['switchLang']))
    {
   		$chat->setIP();
   		erLhcoreClassModelChat::detectLocation($chat);
   		
   		$statusGeoAdjustment = erLhcoreClassChat::getAdjustment(erLhcoreClassModelChatConfig::fetch('geoadjustment_data')->data_value, $inputData->vid);
   		
   		if ($statusGeoAdjustment['status'] == 'hidden') { // This should never happen
   			exit('Chat not available in your country');
   		}
   		
   		// Because product can have different department than selected product, we reasign chat to correct department if required
   		if ($chat->product_id > 0) {
   		    $chat->dep_id = $chat->product->departament_id;
   		}
   		
   		if ( (isset($additionalParams['offline']) && $additionalParams['offline'] == true) || $statusGeoAdjustment['status'] == 'offline') {
	   		
   		    $attributePresend = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chat_offline_request_presend',array(
   		        'input_data' => $inputData,
   		        'chat' => $chat,
   		        'prefill' => array('chatprefill' => isset($chatPrefill) ? $chatPrefill : false)));

   		    if (!isset($attributePresend['status']) || $attributePresend['status'] !== erLhcoreClassChatEventDispatcher::STOP_WORKFLOW) {
   		       erLhcoreClassChatMail::sendMailRequest($inputData,$chat,array('chatprefill' => isset($chatPrefill) ? $chatPrefill : false));
   		    }

   			if (isset($chatPrefill) && ($chatPrefill instanceof erLhcoreClassModelChat)) {
   				erLhcoreClassChatValidator::updateInitialChatAttributes($chatPrefill, $chat);
   			}

   			erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chat_offline_request',array(
   			'input_data' => $inputData,
   			'chat' => $chat,
   			'prefill' => array('chatprefill' => isset($chatPrefill) ? $chatPrefill : false)));

   		    // Save as offline request
            $chat->time = time();
            $chat->lsync = time();
            $chat->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;
            $chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_OFFLINE_REQUEST;
            $chat->hash = erLhcoreClassChat::generateHash();
            $chat->referrer = isset($_POST['URLRefer']) ? $_POST['URLRefer'] : '';
            $chat->session_referrer = isset($_POST['r']) ? $_POST['r'] : '';
            if ( empty($chat->nick) ) {
                $chat->nick = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Visitor');
            }
            $chat->saveThis();

			if ( $inputData->question != '' ) {
				// Store question as message
				$msg = new erLhcoreClassModelmsg();
				$msg->msg = trim($inputData->question);
				$msg->chat_id = $chat->id;
				$msg->user_id = 0;
				$msg->time = time();
				erLhcoreClassChat::getSession()->save($msg);

				$chat->unanswered_chat = 1;
				$chat->last_msg_id = $msg->id;
				$chat->saveThis();
			}

	   		$tpl->set('request_send',true);
	   	} else {
	       $chat->time = time();
	       $chat->status = 0;
	       
	       $chat->hash = erLhcoreClassChat::generateHash();
	       $chat->referrer = isset($_POST['URLRefer']) ? $_POST['URLRefer'] : '';
	       $chat->session_referrer = isset($_POST['r']) ? $_POST['r'] : '';

	       if ( empty($chat->nick) ) {
	           $chat->nick = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Visitor');
	       }

	       try {
	           $db = ezcDbInstance::get();
	           $db->beginTransaction();
	       
    	       // Store chat
    	       $chat->saveThis();
    
    	       // Assign chat to user
    	       if ( erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value == 1 && (string)$Params['user_parameters_unordered']['vid'] != '') {
    	            // To track online users
    	            $userInstance = erLhcoreClassModelChatOnlineUser::handleRequest(array('message_seen_timeout' => erLhcoreClassModelChatConfig::fetch('message_seen_timeout')->current_value, 'check_message_operator' => true, 'vid' => (string)$Params['user_parameters_unordered']['vid']));
    
    	            if ($userInstance !== false) {
    	                $userInstance->chat_id = $chat->id;
    	                $userInstance->dep_id = $chat->dep_id;
    	                $userInstance->message_seen = 1;
    	                $userInstance->message_seen_ts = time();
    	                $userInstance->saveThis();
    
    	                $chat->online_user_id = $userInstance->id;
    
    	                if ( erLhcoreClassModelChatConfig::fetch('track_footprint')->current_value == 1) {
    		            	erLhcoreClassModelChatOnlineUserFootprint::assignChatToPageviews($userInstance);
    		            }
    	            }
    	       }
    
    	       $messageInitial = false;
    	       
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
    	               
    	               $messageInitial = $msg;
    	               
    	               $chat->unanswered_chat = 1;
    	               $chat->last_msg_id = $msg->id;
    	               $chat->saveThis();
    	           }
    	       }
    
    			// Auto responder
    			$responder = erLhAbstractModelAutoResponder::processAutoResponder($chat);
    
    			if ($responder instanceof erLhAbstractModelAutoResponder) {
    				$beforeAutoResponderErrors = array();
    				erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_auto_responder_triggered', array('chat' => & $chat, 'errors' => & $beforeAutoResponderErrors));
    
    				if (empty($beforeAutoResponderErrors)) {
    				    
    				    $responderChat = new erLhAbstractModelAutoResponderChat();
    				    $responderChat->auto_responder_id = $responder->id;
    				    $responderChat->chat_id = $chat->id;
    				    $responderChat->wait_timeout_send = 1 - $responder->repeat_number;
    				    $responderChat->saveThis();
    				    
    				    $chat->auto_responder_id = $responderChat->id;
    				        					
    					if ($responder->wait_message != '') {
    						$msg = new erLhcoreClassModelmsg();
    						$msg->msg = trim($responder->wait_message);
    						$msg->chat_id = $chat->id;
    						$msg->name_support = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support');
    						$msg->user_id = -2;
    						$msg->time = time() + 5;
    						erLhcoreClassChat::getSession()->save($msg);
    
    						if ($chat->last_msg_id < $msg->id) {
    							$chat->last_msg_id = $msg->id;
    						}
    					}
    
    					
    					erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.auto_responder_triggered', array('chat' => & $chat));
    
    					$chat->saveThis();
    				} else {
    					$msg = new erLhcoreClassModelmsg();
    					$msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Auto responder got error').': '.implode('; ', $beforeAutoResponderErrors);
    					$msg->chat_id = $chat->id;
    					$msg->user_id = -1;
    					$msg->time = time();
    
    					if ($chat->last_msg_id < $msg->id) {
    						$chat->last_msg_id = $msg->id;
    					}
    
    					erLhcoreClassChat::getSession()->save($msg);
    				}
    			}
    
    	       erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chat_started',array('chat' => & $chat, 'msg' => $messageInitial));
    
    	       erLhcoreClassChat::updateDepartmentStats($chat->department);
    	       	       
    	       // Paid chat settings
    	       if (isset($paidChatSettings)) {
    	           erLhcoreClassChatPaid::processPaidChatWorkflow(array(
    	               'chat' => $chat,
    	               'paid_chat_params' => $paidChatSettings,
    	           ));
    	       }

    	       $db->commit();
	       
	       } catch (Exception $e) {
	           $db->rollback();
	           throw $e;
	       }
	       
	       // Redirect user
	       erLhcoreClassModule::redirect('chat/chat/' . $chat->id . '/' . $chat->hash . $themeAppend);
	       exit;
	   	}
    } else {
    	// Show errors only if user is not switching form mode
    	if ($Params['user_parameters_unordered']['switchform'] != 'true' && !isset($_POST['switchLang'])){
    		$tpl->set('errors',$Errors);
    	}        
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
		),
		'req' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'string',
				null,
				FILTER_REQUIRE_ARRAY
		),
		'sh' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'string',
				null,
				FILTER_REQUIRE_ARRAY
		),
		'hattr' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'string',
				null,
				FILTER_REQUIRE_ARRAY
		),
        'value_items_admin' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',
                null,
                FILTER_REQUIRE_ARRAY
        ),
        'via_hidden' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',
                null,
                FILTER_REQUIRE_ARRAY
        ),
        'encattr' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',
                null,
                FILTER_REQUIRE_ARRAY
        ),
        'via_encrypted' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',
                null,
                FILTER_REQUIRE_ARRAY
        )
);

$form = new ezcInputForm( INPUT_GET, $definition );

if ( $form->hasValidData( 'name' ) && !empty($form->name))
{
	$inputData->name_items = $form->name;
}

if ( $form->hasValidData( 'sh' ) && !empty($form->sh))
{
	$inputData->value_show = $form->sh;
}

if ( $form->hasValidData( 'req' ) && !empty($form->req))
{
	$inputData->values_req = $form->req;
}

if ( $form->hasValidData( 'value' ) && !empty($form->value))
{
	$inputData->value_items = $form->value;
}

if ( $form->hasValidData( 'hattr' ) && !empty($form->hattr))
{
	$inputData->hattr = $form->hattr;
}

if ( $form->hasValidData( 'type' ) && !empty($form->type))
{
	$inputData->value_types = $form->type;
}

if ( $form->hasValidData( 'size' ) && !empty($form->size))
{
	$inputData->value_sizes = $form->size;
}

if ( $form->hasValidData( 'encattr' ) && !empty($form->encattr))
{
	$inputData->encattr = $form->encattr;
}

if ( $form->hasValidData( 'via_encrypted' ) && !empty($form->via_encrypted))
{
	$inputData->via_encrypted = $form->via_encrypted;
}

// Fill back office values ir prefilled
if ($form->hasValidData( 'value_items_admin' ))
{
    $inputData->value_items_admin = $form->value_items_admin;
}

if ($form->hasValidData( 'via_hidden' ))
{
    $inputData->via_hidden = $form->via_hidden;
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

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.startchat',array('result' => & $Result,'tpl' => & $tpl, 'params' => & $Params, 'inputData' => & $inputData));

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'userchat';
$Result['show_switch_language'] = true;
$Result['dynamic_height'] = true;
$Result['dynamic_height_adjust'] = '-20';

if (!isset($Result['path'])) {
    $Result['path'] = array(array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Fill in the form to start a chat')));
}

?>
