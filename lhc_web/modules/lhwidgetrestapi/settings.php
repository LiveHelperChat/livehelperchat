<?php

erLhcoreClassRestAPIHandler::setHeaders();

$outputResponse = array(
    'isOnline' => erLhcoreClassChat::isOnline(false, false, array(
        'online_timeout' => (int) erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'],
        'ignore_user_status' => (isset($_GET['ignore_user_status']) && $_GET['ignore_user_status'] == 'true')
    )),
    'hideOffline' => false,
    'vid' => isset($_GET['vid']) ? $_GET['vid'] : substr(sha1(mt_rand() . microtime()),0,20)
);

$ignorable_ip = erLhcoreClassModelChatConfig::fetch('ignorable_ip')->current_value;
$fullHeight = (isset($Params['user_parameters_unordered']['fullheight']) && $Params['user_parameters_unordered']['fullheight'] == 'true') ? true : false;

if ( $ignorable_ip == '' || !erLhcoreClassIPDetect::isIgnored(erLhcoreClassIPDetect::getIP(),explode(',',$ignorable_ip))) {
    //TMP $tpl = erLhcoreClassTemplate::getInstance('lhchat/chatcheckoperatormessage.tpl.php');

    if (is_array($Params['user_parameters_unordered']['department'])){
        erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['department']);
        $department = $Params['user_parameters_unordered']['department'];
    } else {
        $department = false;
    }

    if (is_array($Params['user_parameters_unordered']['ua'])){
        $uarguments = $Params['user_parameters_unordered']['ua'];
    } else {
        $uarguments = false;
    }

    $proactiveInviteActive = erLhcoreClassModelChatConfig::fetch('pro_active_invite')->current_value;

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chatcheckoperatormessage', array('proactive_active' => & $proactiveInviteActive));

    $injectInvitation = array();
    $userInstance = erLhcoreClassModelChatOnlineUser::handleRequest(array('inject_html' => & $injectInvitation, 'tag' => isset($_GET['tag']) ? $_GET['tag'] : false, 'uactiv' => 1, 'wopen' => 0 /*@todo add support if request is made and widget is open, chat is going*/, 'tpl' => & $tpl, 'tz' => $_GET['tz'], 'message_seen_timeout' => erLhcoreClassModelChatConfig::fetch('message_seen_timeout')->current_value, 'department' => $department, 'identifier' => (string)$Params['user_parameters_unordered']['identifier'], 'pages_count' => true, 'vid' => $outputResponse['vid'], 'check_message_operator' => true, 'pro_active_limitation' =>  erLhcoreClassModelChatConfig::fetch('pro_active_limitation')->current_value, 'pro_active_invite' => $proactiveInviteActive));

    // Exit if not required
    $statusGeoAdjustment = erLhcoreClassChat::getAdjustment(erLhcoreClassModelChatConfig::fetch('geoadjustment_data')->data_value,'',false, $userInstance);
    if ($statusGeoAdjustment['status'] == 'offline' || $statusGeoAdjustment['status'] == 'hidden') {
        $outputResponse['hideOffline'] = false;
        $outputResponse['isOnline'] = false;
    }

    if (erLhcoreClassModelChatConfig::fetch('track_footprint')->current_value == 1 && isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
        erLhcoreClassModelChatOnlineUserFootprint::addPageView($userInstance);
    }

    if ($userInstance !== false) {

        if ($userInstance->invitation_id == -1) {
            $userInstance->invitation_id = 0;
            $userInstance->invitation_assigned = true;
            $userInstance->saveThis();
        }

        /*$tpl->set('fullheight', $fullHeight);
        $tpl->set('priority',is_numeric($Params['user_parameters_unordered']['priority']) ? (int)$Params['user_parameters_unordered']['priority'] : false);
        $tpl->set('department',$department !== false ? implode('/', $department) : false);
        $tpl->set('uarguments',$uarguments !== false ? implode('/', $uarguments) : false);
        $tpl->set('operator',is_numeric($Params['user_parameters_unordered']['operator']) ? (int)$Params['user_parameters_unordered']['operator'] : false);
        $tpl->set('theme',is_numeric($Params['user_parameters_unordered']['theme']) && $Params['user_parameters_unordered']['theme'] > 0 ? (int)$Params['user_parameters_unordered']['theme'] : false);
        $tpl->set('visitor',$userInstance);
        $tpl->set('vid',(string)$Params['user_parameters_unordered']['vid']);
        $tpl->set('survey',is_numeric($Params['user_parameters_unordered']['survey']) ? (int)$Params['user_parameters_unordered']['survey'] : false);

        $tag = false;
        if (isset($_GET['tag'])) {
            $tag = implode(',',array_unique(explode(',',$_GET['tag'])));
        }

        $tpl->set('tag', $tag);

        $dynamic = true;

        if ($userInstance->reopen_chat == 1 && ($chat = $userInstance->chat) !== false && $chat->user_status == erLhcoreClassModelChat::USER_STATUS_PENDING_REOPEN) {
            $tpl->set('reopen_chat',$chat);
            $dynamic = false;
        }

        // Execute request only if widget is not open
        if ($userInstance->operation != '' && (int)$Params['user_parameters_unordered']['wopen'] == 0) {
            $tpl->set('operation',$userInstance->operation);
            $userInstance->operation = '';
            $userInstance->operation_chat = '';
            $userInstance->saveThis();
        }

        // If there is no assigned default proactive invitations find dynamic one triggers
        $dynamicEverytime = $userInstance->invitation instanceof erLhAbstractModelProactiveChatInvitation && $userInstance->invitation->dynamic_invitation == 1 && $userInstance->invitation->show_instant == 0;

        if ($dynamic == true && $userInstance->message_seen == 0 && ($userInstance->operator_message == '' || $dynamicEverytime == true) && (int)$Params['user_parameters_unordered']['wopen'] == 0) {
            $tpl->set('dynamic_processed',is_array($Params['user_parameters_unordered']['dyn']) ? $Params['user_parameters_unordered']['dyn'] : array());
            $tpl->set('dynamic',$dynamic);
            $tpl->set('dynamic_everytime',$dynamicEverytime);
            $tpl->set('dynamic_invitation', erLhcoreClassModelChatOnlineUser::getDynamicInvitation(array('online_user' => $userInstance, 'tag' => isset($_GET['tag']) ? $_GET['tag'] : false)));
        }

        if ((int)$Params['user_parameters_unordered']['count_page'] == 1) {
            $tpl->set('inject_html', erLhcoreClassModelChatOnlineUser::getInjectHTMLInvitation(array('online_user' => $userInstance, 'tag' => isset($_GET['tag']) ? $_GET['tag'] : false)));
        }

        echo $tpl->fetch();*/
    }
}

erLhcoreClassRestAPIHandler::outputResponse($outputResponse);
exit();