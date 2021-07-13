<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/sendnotice.tpl.php');

$visitor = erLhcoreClassModelChatOnlineUser::fetch((int)$Params['user_parameters']['online_id']);

$userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID());
$tpl->set('visitor',$visitor);
$tpl->set('limitDepartments',$userDepartments !== true ? array('filterin' => array('id' => $userDepartments)) : array());

if ( isset($_POST['SendMessage']) ) {

    $validationFields = array();
    $validationFields['Message'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    $validationFields['RequiresEmail'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    $validationFields['RequiresUsername'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    $validationFields['RequiresPhone'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    $validationFields['AssignToMe'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    $validationFields['FullWidget'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    $validationFields['IgnoreAutoresponder'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    $validationFields['CampaignId'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1) );

    $form = new ezcInputForm( INPUT_POST, $validationFields );    
    $Errors = array();
    
    if ( !$form->hasValidData( 'Message' ) || $form->Message == '' ) {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter your message');
    } elseif ($form->hasValidData( 'Message' )) {
        $visitor->operator_message = $form->Message;
    }

    if ($form->hasValidData( 'Message' ) && $form->Message != '' && mb_strlen($form->Message) > (int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value) {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Maximum').' '.(int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value.' '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','characters for a message');
    }

    if ($form->hasValidData( 'RequiresEmail' ) && $form->RequiresEmail == true) {
        $visitor->requires_email = 1;
    } else {
    	$visitor->requires_email = 0;
    }

    if ($form->hasValidData( 'RequiresUsername' ) && $form->RequiresUsername == true) {
        $visitor->requires_username = 1;
    } else {
    	$visitor->requires_username = 0;
    }

    if ($form->hasValidData( 'RequiresPhone' ) && $form->RequiresPhone == true) {
        $visitor->requires_phone = 1;
    } else {
    	$visitor->requires_phone = 0;
    }

    $onlineAttrSystem = $visitor->online_attr_system_array;

    if ($form->hasValidData( 'AssignToMe' ) && $form->AssignToMe == true) {
        $onlineAttrSystem['lhc_assign_to_me'] = 1;
    } elseif (isset($onlineAttrSystem['lhc_assign_to_me'])) {
        unset($onlineAttrSystem['lhc_assign_to_me']);
    }

    if ($form->hasValidData( 'IgnoreAutoresponder' ) && $form->IgnoreAutoresponder == true) {
        $onlineAttrSystem['lhc_ignore_autoresponder'] = 1;
    } elseif (isset($onlineAttrSystem['lhc_ignore_autoresponder'])) {
        unset($onlineAttrSystem['lhc_ignore_autoresponder']);
    }

    if (($form->hasValidData( 'FullWidget' ) && $form->FullWidget == true)) {
        $onlineAttrSystem['lhc_full_widget'] = 1;
    } elseif (isset($onlineAttrSystem['lhc_full_widget'])) {
        unset($onlineAttrSystem['lhc_full_widget']);
    }
    
    if (isset($onlineAttrSystem['qinv'])) {
        unset($onlineAttrSystem['qinv']);
    }

    $visitor->online_attr_system_array = $onlineAttrSystem;
    $visitor->online_attr_system = json_encode($onlineAttrSystem);

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.sendnotice', array('errors' => & $Errors));
            
    if (count($Errors) == 0) { 
               
        $currentUser = erLhcoreClassUser::instance();   
        $visitor->message_seen = 0;
        $visitor->invitation_id = -1;
        $visitor->operator_user_id = $currentUser->getUserID();

        $campaign = erLhAbstractModelProactiveChatCampaignConversion::findOne(array('filterin' => array('invitation_status' => array(
            erLhAbstractModelProactiveChatCampaignConversion::INV_SEND,
            erLhAbstractModelProactiveChatCampaignConversion::INV_SHOWN,
            erLhAbstractModelProactiveChatCampaignConversion::INV_SEEN
        )),'filter' => array('vid_id' => $visitor->id)));

        if (!($campaign instanceof erLhAbstractModelProactiveChatCampaignConversion)) {
            $campaign = new erLhAbstractModelProactiveChatCampaignConversion();
        }

        $campaign->vid_id = $visitor->id;
        $campaign->invitation_status = erLhAbstractModelProactiveChatCampaignConversion::INV_SEND;
        $campaign->ctime = time();
        $campaign->con_time = time();
        $campaign->department_id = $visitor->dep_id;

        $detect = new Mobile_Detect;
        $detect->setUserAgent($visitor->user_agent);
        $campaign->device_type = ($detect->isMobile() ? ($detect->isTablet() ? 2 : 1) : 0);

        if ($form->hasValidData( 'CampaignId' )) {
            $campaign->campaign_id = $form->CampaignId;
        }

        $campaign->saveThis();

        $visitor->conversion_id = $campaign->id;

        // Operator want's to start a chat
        if (isset($_POST['SendMessage']) && $_POST['SendMessage'] == 2) {
            $chatPast = erLhcoreClassModelChat::fetch($visitor->chat_id);
            if (!($chatPast instanceof erLhcoreClassModelChat) || !in_array($chatPast->status,array(erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,erLhcoreClassModelChat::STATUS_PENDING_CHAT)) || in_array($chatPast->status_sub,array(erLhcoreClassModelChat::STATUS_SUB_SURVEY_COMPLETED, erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW,erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT,erLhcoreClassModelChat::STATUS_SUB_CONTACT_FORM))) {

                $chat = new erLhcoreClassModelChat();
                $chat->hash = erLhcoreClassChat::generateHash();

                if ($chatPast instanceof erLhcoreClassModelChat) {
                    $chat->nick = $chatPast->nick;
                }

                if (erLhcoreClassModelChatConfig::fetch('remember_username')->current_value == 1) {
                    if ($chat->nick == 'Visitor' || empty($chat->nick)) {
                        if ($visitor->nick && $visitor->has_nick) {
                            $chat->nick = $visitor->nick;
                        }
                    }
                }

                if (empty($chat->nick)) {
                    $chat->nick = 'Visitor';
                }

                $chat->time = $chat->pnd_time = time() - 1;
                $chat->lsync = time();
                $chat->ip = $visitor->ip;
                $chat->online_user_id = $visitor->id;
                $chat->user_tz_identifier = $visitor->visitor_tz;
                $chat->device_type = $visitor->device_type - 1;
                $chat->uagent = $visitor->user_agent;
                $chat->dep_id = $visitor->dep_id;
                $chat->wait_time = 1;

                erLhcoreClassModelChat::detectLocation($chat, $visitor->vid);

                $attributesSystem = $onlineAttrSystem;
                foreach ($visitor->additional_data_array as $keyItem => $additionalItem) {
                    $attributesSystem[$additionalItem['identifier']] = $additionalItem['value'];
                }

                $chat->saveThis();

                erLhcoreClassChatValidator::validateJSVarsChat($chat, $attributesSystem);

                $chatPast = $chat;
                // Set new chat id
                $visitor->chat_id = $chat->id;
            }

            $chatPast->user_id = $chatPast->user_id > 0 ? $chatPast->user_id : erLhcoreClassUser::instance()->getUserID();

            if ($chatPast->dep_id == 0) {
                $chatPast->dep_id = (int)$_POST['DepartmentID'];
            }

            if (erLhcoreClassModelDepartament::getCount(array('filter' => array('id' => $chatPast->dep_id))) == 0) {
                $department = erLhcoreClassModelDepartament::findOne(array('sort' => 'hidden ASC, priority ASC', 'limit' => 1,'filter' => array('disabled' => 0)));
                $chatPast->dep_id = $department->id;
            }

            // Save message as a chat message
            $msg = new erLhcoreClassModelmsg();
            $msg->msg = $visitor->operator_message;
            $msg->time = time() - 1;
            $msg->chat_id = $chatPast->id;
            $msg->user_id = $chatPast->user_id;
            $msg->name_support = (string)$chatPast->plain_user_name;
            $msg->saveThis();

            $chatPast->status_sub = erLhcoreClassModelChat::STATUS_SUB_DEFAULT;
            $chatPast->last_msg_id = $msg->id;
            $chatPast->updateThis();

            // During next check message from operator event fetch chat information
            $onlineAttrSystem['lhc_start_chat'] = $chatPast->id;

            $visitor->online_attr_system_array = $onlineAttrSystem;
            $visitor->online_attr_system = json_encode($onlineAttrSystem);

            $tpl->set('start_chat',true);
            $tpl->set('chat',$chatPast);
        }

        $visitor->saveThis();

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('onlineuser.proactive_send_invitation', array('ou' => & $visitor));

        $tpl->set('message_saved',true);
    } else {        
        $tpl->set('errors',$Errors);
    } 
}

echo $tpl->fetch();
exit;

?>