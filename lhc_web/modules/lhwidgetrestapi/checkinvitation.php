<?php

erLhcoreClassRestAPIHandler::setHeaders();

$ignorable_ip = erLhcoreClassModelChatConfig::fetch('ignorable_ip')->current_value;

$outputResponse = array('status' => true);

if ( $ignorable_ip != '' && erLhcoreClassIPDetect::isIgnored(erLhcoreClassIPDetect::getIP(),explode(',',$ignorable_ip))) {
    erLhcoreClassRestAPIHandler::outputResponse(array('status' => false));
    exit;
}

if (isset($_GET['dep'])) {
    $department = explode(',',$_GET['dep']);
    erLhcoreClassChat::validateFilterIn($department);
} else {
    $department = false;
}

if (isset($_GET['ua'])) {
    $uarguments = explain(',',$_GET['ua']);
} else {
    $uarguments = false;
}

$proactiveInviteActive = erLhcoreClassModelChatConfig::fetch('pro_active_invite')->current_value;

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chatcheckoperatormessage', array('proactive_active' => & $proactiveInviteActive));

$injectInvitation = array();

$paramsRequest = array(
    'inject_html' => & $injectInvitation,
    'tag' => isset($_GET['tag']) ? $_GET['tag'] : false,
    'uactiv' => (isset($_GET['uactiv']) ? 1 : 0),
    'wopen' => (isset($_GET['wopen']) ? 1 : 0),
    //'tpl' => & $tpl,
    'message_seen_timeout' => erLhcoreClassModelChatConfig::fetch('message_seen_timeout')->current_value,
    'department' => $department,
    'identifier' => (isset($_GET['idnt']) ? $_GET['idnt'] : ''),
    'pages_count' => false,
    'vid' => (string)$_GET['vid'],
    'check_message_operator' => true,
    'pro_active_limitation' =>  erLhcoreClassModelChatConfig::fetch('pro_active_limitation')->current_value,
    'pro_active_invite' => $proactiveInviteActive);

$userInstance = erLhcoreClassModelChatOnlineUser::handleRequest($paramsRequest);

// Exit if not required
$statusGeoAdjustment = erLhcoreClassChat::getAdjustment(erLhcoreClassModelChatConfig::fetch('geoadjustment_data')->data_value,'',false,$userInstance);

if ($statusGeoAdjustment['status'] == 'offline' || $statusGeoAdjustment['status'] == 'hidden' || $userInstance === false) {
    erLhcoreClassRestAPIHandler::outputResponse(array('status' => false));
    exit;
}

$outputResponse['vid_id'] = $userInstance->id;

if ($userInstance->invitation_id == -1) {
    $userInstance->invitation_id = 0;
    $userInstance->invitation_assigned = true;
    $userInstance->saveThis();
}

$dynamic = true;

if ($userInstance->reopen_chat == 1 && ($chat = $userInstance->chat) !== false && $chat->user_status == erLhcoreClassModelChat::USER_STATUS_PENDING_REOPEN) {
    $reopen_chat = $chat;
    $dynamic = false;
}

// If there is no assigned default proactive invitations find dynamic one triggers
$dynamicEveryTime = $userInstance->invitation instanceof erLhAbstractModelProactiveChatInvitation && $userInstance->invitation->dynamic_invitation == 1 && $userInstance->invitation->show_instant == 0;

if ($dynamic == true && $userInstance->message_seen == 0 && ($userInstance->operator_message == '' || $dynamicEveryTime == true) && !isset($_GET['wopen'])) {
    if (isset($_GET['init']) && $_GET['init'] == 1) {
        $dynamicProcessed = isset($_GET['dyn']) ? explode(',', $_GET['dyn']) : array();
        $dynamic_invitation = erLhcoreClassModelChatOnlineUser::getDynamicInvitation(array('online_user' => $userInstance, 'tag' => isset($_GET['tag']) ? $_GET['tag'] : false));
        foreach ($dynamic_invitation as $dynamicInvitation) {
            if (in_array($dynamicInvitation->id, $dynamicProcessed)) {
                continue; // Skip if particular invitation was already shown
            }
            $outputResponse['dynamic'][] = array(
                'id' => $dynamicInvitation->id,
                'type' => $dynamicInvitation->event_type,
                'iddle_for' => $dynamicInvitation->iddle_for,
                'inject_html' => isset($dynamicInvitation->design_data_array['inject_html']) && $dynamicInvitation->design_data_array['inject_html'] != ''
            );
        }
    }
}

if (isset($reopen_chat)) {
    $outputResponse['reopen'] = array(
        'id' => $reopen_chat->id,
        'hash' => $reopen_chat->hash
    );
} elseif ($userInstance->has_message_from_operator == true && (!isset($dynamicEveryTime) || $dynamicEveryTime == false)) {
    $outputResponse['status'] = false;

    if ($userInstance->invitation instanceof erLhAbstractModelProactiveChatInvitation && $userInstance->invitation->show_on_mobile == 1) {

        if (($userInstance->invitation_assigned == false && $userInstance->invitation->delay > 0) || $userInstance->invitation->delay_init > 0) {
            $outputResponse['delay'] = ($userInstance->invitation_assigned == true ? $userInstance->invitation->delay_init : $userInstance->invitation->delay) * 1000;
        }

        if (isset($userInstance->invitation->design_data_array['mobile_html']) && $userInstance->invitation->design_data_array['mobile_html'] != '') {

            if (isset($userInstance->invitation->design_data_array['mobile_style']) && $userInstance->invitation->design_data_array['mobile_style'] != '') {

                $replaceStyleArray = array();

                for ($i = 1; $i < 5; $i++) {
                    $replaceStyleArray['{proactive_img_' . $i . '}'] = erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value . '//' . $_SERVER['HTTP_HOST'] . $userInstance->invitation->{'design_data_img_' . $i . '_url'};
                }

                $contentCSS = str_replace(array_keys($replaceStyleArray), array_values($replaceStyleArray), $userInstance->invitation->design_data_array['mobile_style']);
                $contentCSS = str_replace(array("\n", "\r"), '', $contentCSS);
                $outputResponse['site_css'] = json_encode($contentCSS);
            }

            $outputResponse['html_invitation'] = json_encode(str_replace(array("\n", "\r", '{readmessage}', '{hideInvitation}'), array('', '', "return lh_inst.showHTMLInvitation(lh_inst.invitationURL)", "return lh_inst.hideHTMLInvitation()"), $userInstance->invitation->design_data_array['mobile_html']));

        } else {
            $outputResponse['invitation'] = $userInstance->invitation->id;
        }

    } else {
        if ($userInstance->invitation instanceof erLhAbstractModelProactiveChatInvitation) {
            if (($userInstance->invitation_assigned == false && $userInstance->invitation->delay > 0) || $userInstance->invitation->delay_init > 0) {
                $outputResponse['delay'] = ($userInstance->invitation_assigned == true ? $userInstance->invitation->delay_init : $userInstance->invitation->delay) * 1000;
            }

            if (isset($userInstance->invitation->design_data_array['inject_html']) && $userInstance->invitation->design_data_array['inject_html'] != '') {
                $outputResponse['inject_html'] = true;
            }

            $outputResponse['invitation'] = $userInstance->invitation->id;
        } else {
            $outputResponse['invitation'] = true;
        }
    }
}

if ($userInstance->operation != '' && (int)$_GET['wopen'] == 0) {
    $outputResponse['operation'] = $userInstance->operation;
    $userInstance->operation = '';
    $userInstance->operation_chat = '';
    $userInstance->saveThis();
}

if (isset($_GET['init']) && $_GET['init'] == 1) {

    if ($userInstance->next_reschedule > 0) {
        $outputResponse['next_reschedule'] = ($userInstance->next_reschedule + 1) * 1000;
    }

    $injectInvitations = erLhcoreClassModelChatOnlineUser::getInjectHTMLInvitation(array('online_user' => $userInstance, 'tag' => isset($_GET['tag']) ? $_GET['tag'] : false));
    foreach ($injectInvitations as $injectInvitation) {
        $outputResponse['dynamic'][] = array(
            'id' => $injectInvitation->id,
            'type' => $injectInvitation->event_type,
            'iddle_for' => $injectInvitation->iddle_for,
            'only_inject' => true,
            'inject_html' => true,
            'every_time' => !(!isset($injectInvitation->design_data_array['dynamic_everytime']) || $injectInvitation->design_data_array['dynamic_everytime'] == 0)
        );
    }
}

$outputResponse['qinv'] = isset($userInstance->online_attr_system_array['qinv']);

erLhcoreClassRestAPIHandler::outputResponse($outputResponse);
exit;

?>