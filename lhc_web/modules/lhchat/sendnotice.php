<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/sendnotice.tpl.php');

$visitor = erLhcoreClassModelChatOnlineUser::fetch((int)$Params['user_parameters']['online_id']);

$tpl->set('visitor',$visitor);

if ( isset($_POST['SendMessage']) ) {
    
    $validationFields = array();
    $validationFields['Message'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    $validationFields['RequiresEmail'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    $validationFields['RequiresUsername'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    $validationFields['RequiresPhone'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    $validationFields['AssignToMe'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
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